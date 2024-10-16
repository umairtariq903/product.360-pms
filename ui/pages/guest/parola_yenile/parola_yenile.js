function parolaDegistir(obj) {
    var parola = $("#newPassword").val();
    var parolaTekrar = $("#newPasswordRepeat").val();

    if (parola == "")
        return toastr.error("Parola alanı boş bırakılamaz","Hata",{ timeOut: 2000 });
    if (parola != parolaTekrar)
        return toastr.error("Parola ve Parola Tekrar alanları birbirine eşit olmalıdır","Hata",{ timeOut: 2000 });
    Page.Ajax.Send("ParolaDegistir", parola, function (resp) {
        if (resp == 1)
        {
            $(obj).attr("disabled",true);
            toastr.success("Parola değiştirme işlemi başarıyla tamamlandı.Yönlendiriliyorsunuz.","Başarılı",{ timeOut: 2000 });
            setTimeout(function () {
                Page.Load(SITE_URL);
            },2000);
        }
        else
            return toastr.error(resp,"Hata",{ timeOut: 3000 });
    });
}
