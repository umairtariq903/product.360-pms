
function RunImport(rowId, tr, a)
{
    let fromAt = $(tr[0]).attr("fromat");
    if(fromAt == 1)
        Page.Open(PAGE_AdminRunImport,{import_id: rowId});
    if(fromAt == 2 || fromAt == 3)
    {
        Page.Ajax.SendBool("AddWorkImport",rowId,function(resp){
            Page.ShowSuccess("İşlem başarıyla tamamlandı.Arka planda işlem devam etmektedir.");
        }, "Import işlemi sıraya ekleniyor...");
    }
}
