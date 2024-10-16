TEL_MASK = 2;

$(function () {
    let islem = Page.GetParameters(ORIGINAL_URL).islem;
    if (typeof islem == "undefined")
        islem = "login";
    FieldGoster(islem);
    var hatirla = Cookie.get(APP_KOD + '_hatirla');
    if(hatirla)
        SetAllCookie();
    $("#AlanKodu").change();
})

function AlanKoduDegisti(obj) {
    if ($(obj).val() == "+90")
        Mask.Telefon("#Telefon");
    else
        $("#Telefon").unmask().removeClass("hasMask");
}

function FieldGoster(fName)
{
    $(".field-div").hide();
    $("."+fName+"-field-div").show();

}
function SetAllCookie()
{
    $('#Email').val(Cookie.get(APP_KOD + '_email'));
    // $('#Parola').val(Cookie.get(APP_KOD + '_parola'));
    $('#Hatirla').attr('checked', Cookie.get(APP_KOD + '_hatirla') == 'true');
}
function GirisYap()
{
    var obj = {};

    obj.Email = $("#Email").val();
    obj.Password = $("#Password").val();
    obj.Hatirla = $("#Hatirla").is(":checked");

    if(obj.Email == "" || obj.Password == "")
        return Page.ShowError("Lütfen tüm alanları doldurunuz..");

    if (obj.Hatirla && typeof ClientJS == "function")
    {
        var client = new ClientJS(); // Create A New Client Object
        var ua = client.getBrowserData().ua;
        var canvasPrint = client.getCanvasPrint();

        var fingerprint = client.getCustomFingerprint(ua, canvasPrint);

        obj.cl = fingerprint;
    }

    Page.Ajax.SendBool("GirisYap", obj, function(){
        Cookie.set(APP_KOD + '_hatirla', obj.Hatirla);
        if(obj.Hatirla)
        {
            Cookie.set(APP_KOD + '_email', obj.Email);
            // Cookie.set(APP_KOD + '_parola', obj.Parola);
        }
        else
        {
            Cookie.set(APP_KOD + '_email', "");
            Cookie.set(APP_KOD + '_parola', "");
        }
        Page.Refresh();
    },"Logging in");
}

function ParolaSifirla()
{
    if (Cookie.get("ParolaSifirla") != "")
        return toastr.error("1 dakika sonra tekrar deneyiniz","Hata",{ timeOut: 2000 });
    var Email = $("#Email").val();

    if(Email == "")
        return toastr.error("Geçerli bir email adresi giriniz","Hata",{ timeOut: 2000 });

    Page.Ajax.Send("ParolaSifirla", Email, function(resp){
        Cookie.setWithTime("ParolaSifirla",1,1);
        if (resp == 1)
        {
            $("#Email").val("");
            toastr.success("Parola sıfırlama linki email adresine gönderilmiştir.Spam klasörünü kontrol ediniz.","Başarılı",{ timeOut: 2000 });
            GonderilecekMailKontrol();
            setTimeout(function () {
                Page.Refresh();
            },2000);
        }
        else
            return toastr.error(resp,"Hata",{ timeOut: 2000 });
    },"Kontrol ediliyor");
}

function KayitOl(btnObj)
{
    var obj = {};
    obj.Ad = $("#Ad").val();
    obj.Soyad = $("#Soyad").val();
    obj.AlanKodu = $("#AlanKodu").val();
    obj.Email = $("#Email").val();
    obj.Parola = $("#Parola").val();
    obj.ParolaTekrar = $("#ParolaTekrar").val();
    obj.Hatirla = $("#Hatirla").is(":checked");

    if(obj.Ad == "" || obj.Soyad == "" || obj.AlanKodu == "" || obj.Email == "" || obj.Parola == "")
        return toastr.error("Tüm alanları doldurunuz","Hata",{ timeOut: 2000 });

    obj.Telefon = "";
    let telParts = $("#Telefon").val().match(/\d/g)
    if (telParts)
        obj.Telefon = telParts.join('');
    if (obj.Telefon == "")
        return toastr.error("Telefon alanı geçersiz","Hata",{ timeOut: 2000 });

    if (! isValidEmail(obj.Email))
        return toastr.error("Geçerli bir mail adresi giriniz","Hata",{ timeOut: 2000 });

    if (obj.Parola != obj.ParolaTekrar)
        return toastr.error("Parola ve Parola Tekrar alanları birbirine eşit olmalıdır","Hata",{ timeOut: 2000 });

    $(btnObj).attr("disabled",true);
    Page.Ajax.Send("KayitOl", obj, function(resp){
        if (resp == 1)
        {
            Cookie.set(APP_KOD + '_hatirla', obj.Hatirla);
            Cookie.set(APP_KOD + '_email', obj.Email);
            toastr.success("Kayıt oluşturuldu.Profil sayfasına yönlendiriliyorsunuz","Başarılı",{ timeOut: 2000 });
            setTimeout(function () {
                Page.Refresh();
            },2000);
        }
        else
        {
            $(btnObj).attr("disabled",false);
            return toastr.error(resp,"Hata",{ timeOut: 2000 });
        }
    },"Kayıt oluşturuluyor");
}
