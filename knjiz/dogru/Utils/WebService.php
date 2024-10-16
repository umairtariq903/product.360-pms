<?php
class WebService
{
	const TYP_BOOL = 'xsd:boolean';
	const TYP_STR = 'xsd:string';
	const TYP_INT = 'xsd:integer';
	const TYP_FLT = 'xsd:decimal';
	const TYP_ANY = 'xsd:anything';

	public $WsdlFile = 'prv/service.wsdl';
	public $ServiceName = 'ErsWebService';
	/**
	 * @var nusoap_server
	 */
	public $Server = null;

	public static $IgrnoreFields = array();

	public static $AllowedFields = array();

	public static $TypeAlias = array();

	public function __construct()
	{
		header ('Content-type:text/html; charset=UTF-8');
		ini_set("soap.wsdl_cache_enabled", "0");

		$this->CheckWsdlFile();
		if (!$this->CheckAuthentication())
			exit;
	}

	private function CheckAuthentication()
	{
		$user = '';
		$pass = '';
		if (isset($_SERVER['PHP_AUTH_USER']))
		{
			$user = $_SERVER['PHP_AUTH_USER'];
			$pass = $_SERVER['PHP_AUTH_PW'];
		}

		$auth = $this->AuthenticateGetCache($user, $pass);
		if ($auth != TRUE)
		{
			header('WWW-Authenticate: Basic realm=""');
			header('HTTP/1.0 401 Unauthorized');
			echo "WebServis login error: $auth";
			return FALSE;
		}
		return TRUE;
	}

	private function AuthenticateGetCache($user, $pass)
	{
		$file = 'prv/ws_users.cache';
		$key = $user;
		$users = array();
		if (file_exists($file))
			$users = (array)@json_decode(file_get_contents($file));
		// 5 dakikadan yeni bir istek ise tekrar kontrole gerek yok
		if (time() - IfNull($users, $key, 0) < 5*60)
			return TRUE;
		$users[$key] = time();
		$auth = $this->AuthenticateUser($user, $pass);
		if ($auth)
			file_put_contents($file, json_encode($users));
		return $auth;
	}

	protected function RegisterMethods()
	{
		// Metotların isimlerini alıyoruz
		$reflector = new ReflectionClass($this);
		$allMethods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach($allMethods as $method)
		{
			/* @var $method ReflectionMethod */
			if (! $method->isStatic() || $method->class != get_class($this))
				continue;
			$lines = explode("\n", $method->getDocComment());
			$input = array();
			$output = self::TYP_STR;
			$description = '';
			$regs = array();
			$stripped = '';
			foreach($lines as $line)
			{
				if (preg_match('/\*\s+\@param\s+([^\s]+)\s+\$([^\s]+)\s*(.*)/', $line, $regs))
				{
					$type = $this->ConvertType($regs[1]);
					$input[$regs[2]] = array('type' => $type, 'title' => $regs[3]);
					if (substr($type, 0, 3) == 'tns')
						$this->AddDbModelTypes (substr($type, 4));
				}
				else if (preg_match("/\*\s+@return\s+([^\s]+)/", $line, $regs))
				{
					$output = $this->ConvertType($regs[1]);
					if (substr($output, 0, 3) == 'tns')
						$output = 'tns:' . $this->AddDbModelTypes(substr($output, 4));
				}
				else
				{
					$stripped = trim(str_replace(array("*", "/"), '', $line));
					if ($stripped)
						$description .= $stripped . ' ';
				}
			}
			$this->register($method->name, $input, $output, $description);
		}
	}

	protected function ConvertType($typeStr)
	{
		switch ($typeStr)
		{
			case 'bool':
			case 'boolean':
				return self::TYP_BOOL;
			case 'string': return self::TYP_STR;
			case 'integer':
			case 'int': 	return self::TYP_INT;
			case 'decimal':
			case 'double' :
			case 'float'  :
							return self::TYP_FLT;
		}
		return "tns:$typeStr";
	}

	public function AuthenticateUser($userName, $password)
	{
		return true || $userName || $password;
	}

	protected function Register($method, $params, $result, $description)
	{
		$this->Server->register(
				$method, // metod adi
				$params, // giris parametreleri
				array('return' => $result), // cikis parametreleri
				"$this->ServiceName", // namespace
				"$this->ServiceName#$method", // soapaction
				'rpc', // style
				'encoded', // use
				$description  // dokuman
		);
	}

	protected function AddDbModelTypes($modelAd)
	{
		$isArray = substr($modelAd, -2) == '[]';
		if ($isArray)
		{
			$modelAd = substr($modelAd, 0, -2);
			$this->AddDbModelTypes($modelAd);
			$this->AddComplexListType($modelAd);
			return $modelAd . 'List';
		}

		$modelName = IfNull(static::$TypeAlias, $modelAd, $modelAd);
		$fields = array();
		$ignrFields = IfNull(static::$IgrnoreFields, $modelAd, array());
		if (is_string($ignrFields))
			$ignrFields = explode (',', $ignrFields);
		$allowFields = IfNull(static::$AllowedFields, $modelAd, array());
		if (is_string($allowFields))
			$allowFields = explode (',', $allowFields);
		$isModelDb = true;
		if(!is_a($modelAd, 'ModelBase'))
		{
			$fieldList = $allowFields;
			$isModelDb = false;
		}
		else
		{
			$model = new $modelName;
			/*@var $model ModelBase*/
			$fieldList = $model->GetModelMap ()->DbFields;
		}
		foreach($fieldList as $k => $f)
		{
			if($isModelDb)
			{
				if ($allowFields && !in_array($k, $allowFields))
					continue;
				if (!$f->IsReal || $f->LazyInit || in_array($k, $ignrFields))
					continue;
				if ($modelName != $modelAd)
					$type = self::TYP_STR;
				if (is_a($f->GetTypeObj(), 'VarBool'))
					$type = self::TYP_BOOL;
				if (is_a($f->GetTypeObj(), 'VarInt'))
					$type = self::TYP_INT;
				if (is_a($f->GetTypeObj(), 'VarFloat'))
					$type = self::TYP_FLT;
				$fieldName = $k;
				$displayName = $f->DisplayName;
			}
			else
			{
				$parts = explode(':',$f);
				$type = IfNull($parts,1,"string");
				$displayName = IfNull($parts,2,$parts[0]);
				$type = "xsd:$type";
				$fieldName = $parts[0];
			}
			$fields[$fieldName] = array('name' => $fieldName, 'type' => $type, 'title' => $displayName);
		}
		$this->AddComplexType($modelAd, $fields);
		return $modelAd;
	}

	protected function AddComplexType($name, $fields)
	{
		// Notebook tipi tanimla
		$this->Server->wsdl->addComplexType(
				$name, // ozel tip adi
				'complexType', // tip
				'struct', // compositor
				'all', // restrictionBase
				'', // elements
				$fields
		);
	}

	protected function AddComplexListType($name)
	{
		$this->Server->wsdl->addComplexType(
			$name . 'List',
			'complexType',
			'array',
			'',
			'SOAP-ENC:Array',
			array(),
			array(
				array(
					'ref'=>'SOAP-ENC:arrayType',
					'wsdl:arrayType'=>'tns:' .$name. '[]')),
			'tns:' . $name
		);
	}

	private function CheckWsdlFile($print = false)
	{
		if (isset($_GET['recreate']))
			@unlink($this->WsdlFile);
		if (!$print && file_exists($this->WsdlFile))
			return true;

		// NuSoap kullanarak WSDL dosyasını üretiyoruz
		require_once KNJIZ_DIR . '/others/nusoap/nusoap.php';

		// soap_server nesnesi olustur
		$this->Server = $server = new soap_server();
		// WSDL in ilk parametrelerini ver
		$server->configureWSDL($this->ServiceName);
		$server->wsdl->schemaTargetNamespace = App::$WS_URL;
		$this->RegisterMethods();

		if (!isset($HTTP_RAW_POST_DATA))
			$HTTP_RAW_POST_DATA = file_get_contents('php://input');
		if (! file_exists($this->WsdlFile))
		{
			$contents = $server->wsdl->serialize($server->debug_flag);
			file_put_contents($this->WsdlFile, Kodlama::UTF8($contents));
			copy(KNJIZ_DIR . '/others/nusoap/viewer.xsl', dirname($this->WsdlFile) . '/viewer.xsl');
		}

		if ($print)
		{
			header ('Content-type:text/xml; charset=UTF-8');
			echo file_get_contents($this->WsdlFile);
		}

		return false;
	}

	public function handle()
	{
		// Soap Server nesnesi olustur
		$soapServer = new SoapServer($this->WsdlFile, array('encoding' => 'UTF-8'));
		$soapServer->setClass(get_class($this));
		// Soap Server 'i baslat ve gelen istekleri Products sinifina gonder
		if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['wsdl']))
			$soapServer->handle();
		else
			$this->CheckWsdlFile(TRUE);
	}

	protected static function ThrowException($hata, $kod = '')
	{
		throw new SoapFault($kod . $hata, $hata);
	}
}
