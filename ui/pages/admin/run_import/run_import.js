function RunImport()
{
    var dosyaYolu = $("input[type='file']").attr("file_url");
    if(typeof dosyaYolu != "undefined" && dosyaYolu != "")
        Page.Ajax.Send("RunImportFile",dosyaYolu,function(resp){
            console.log(resp);
            Page.ShowSuccess("İşlem başarıyla tamamlandı.");
        }, "Import işlemi gerçekleştiriliyor...");
    else
        Page.ShowError("Import edilecek dosyayı seçin");
}
