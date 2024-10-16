<?php
class AdminVendorExportsDetayPage extends AppPageController
{
    public function Index()
    {
//        $params = ExportRule::AsParams();
//        $params->ExportId = Condition::EQ($_GET["id"]);
//        $this->Rules = ExportRuleDb::Get()->GetList($params);
//        $this->Rules = $this->Data->RulesInfo;
        foreach ($this->Data->RulesInfo as $key => $rule)
        {
            $this->Data->RulesInfo[$key]->FiltersInfo;
            $this->Data->RulesInfo[$key]->TransactionsInfo;
        }
        $this->AddJsVar("RulesInfo",$this->Data->RulesInfo);

        $liste = [];
//        if($this->Data->Id > 0)
//            $liste = $this->Data->FiltersInfo;
        $this->AddJsVar("FiltersData", $liste);
        $pAttributes = PAttributeDb::Get()->SetOrderByExp("id ASC")->GetList();
        $attrs = [
            -1 => "vendor_name",
            -2 => "ALL"
        ];
        foreach ($pAttributes as $pAttribute)
            $attrs[$pAttribute->Id] = $pAttribute->Name;
        asort($attrs);
        $this->AddJsVar("attrs",$attrs);
    }

    public static function SaveNewRule($obj)
    {
        $data = PageController::$_CurrentInstance->Data;
        if($data)
        {
            $rule = new ExportRule();
            $rule->ExportId = $data->Id;
            $rule->Name = $obj->Name;
            $rule->RuleSort = DB::FetchScalar("SELECT IF(MAX(rule_sort),MAX(rule_sort),0)+1 AS new_rule_sort FROM export_rules WHERE export_id=" . $data->Id);
            $rule->Save();
            return $rule->Id;
        }
        return "Export not found";
    }

    public static function SaveRule($obj)
    {
        $rule = ExportRuleDb::Get()->GetById($obj->Id);
        if(! $rule)
            return "Rule not found";
        $rule->SetFromObj($obj);
        return $rule->Save();
    }

    public static function GetRuleSummary($ruleId)
    {
        $snc = new stdClass();
        $rule = ExportRuleDb::Get()->GetById($ruleId);
        if(! $rule)
            return "Rule not found";


        return GetVendorProducts($rule->VendorExportInfo, $ruleId, true);
    }

    public static function DeleteRule($id)
    {
        $rule = ExportRuleDb::Get()->GetById($id);
        if(! $rule)
            return "Rule not found";
        return $rule->Delete();
    }

    public static function ChangeStatusRule($id)
    {
        $rule = ExportRuleDb::Get()->GetById($id);
        if(! $rule)
            return "Rule not found";
        $rule->Aktif = $rule->Aktif ? 0 : 1;
        return $rule->Save();
    }
}
