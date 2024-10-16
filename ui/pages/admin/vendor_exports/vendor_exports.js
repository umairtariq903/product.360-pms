

function LinkKopyala(rowId, tr, a)
{
    metniPanoyaKopyalaEski($(tr).attr("link"));
}
function metniPanoyaKopyalaEski(metin)
{
    const textarea = document.createElement('textarea');
    textarea.value = metin;
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        console.log('Metin başarıyla panoya kopyalandı.');
    } catch (err) {
        console.error('Metin panoya kopyalanırken bir hata oluştu: ', err);
    }
    document.body.removeChild(textarea);
    swal( {
        title: "Success",
        text: "Copied",
        type: 'success',
        buttons: false,
        timer: 500
    }, null)
}

function Guncelle(rowId, tr, a)
{
    Page.Ajax.SendBool("Guncelle",rowId,function () {
        Page.Refresh();
        Page.ShowSuccess("Güncellendi");
    },"Processing...")
}

function Indir(rowId, tr, a)
{
    Page.Download($(tr).attr("link"));
}
