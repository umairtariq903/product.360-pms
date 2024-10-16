function CsvIndir(rowId, tr, a)
{
    setTimeout(function () {
        Page.Ajax.Send("CsvDosyaUrlGetir", rowId, function (resp) {
            window.open(resp,"_blank");
        });
    },1000);
}
