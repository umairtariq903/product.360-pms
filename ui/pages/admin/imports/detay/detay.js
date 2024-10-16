$(function () {
    var AttributesModal = {
        Fields: {
            AttributeId: {D: 'Attribute',T:attrs,CS: 1, RQ: 1},
            // Adet: {D: 'Adet',T:"int",RQ: 1,CS: 1},
            Value: {D: 'Key',T:'text',CS: 1,Attr: {"placeholder": "Default Key (Attribute Name)"}},
            Condition: {D: 'Condition',T:'text',CS: 1,Attr: {"placeholder": "== 1 OR != 1"}},
            // BelgeTarihi: {D: 'Belge Tarihi',T:'date',CS: 1},
            // ParaBirimi: {D: 'Para Birimi',T:{1: "TL", 2: "Dolar", 3: "Euro"},RQ: 1,CS: 1},
            // Fiyat: {D: 'Fiyat',T:'money',CS: 1,Attr: {"unit": " "}},
        },
        // MinRowCount: 1,
        /*OnBeforeShow: function () {
            var select = $('select[field="DogruCevap"]');
            var options = select.find('option');

            options.sort(function(a, b) {
                return $(a).text().localeCompare($(b).text());
            });
            select.html(options);
        },*/
        Dialog: {C: 'Ekle', W: 500, H: 320},
        // RowTemplate: "Deneme",
        MinRowCount: 1,
        Insertable: true,
        Deletable:  true,
        DetayForm:  false,
        InlineEdit: true,
        RowNumbers: true
    }
    JsTable.Init('Attributes', AttributesModal, AttributesData);
    Jui.InitInputs("#Attributes");

    $("#FromAt").on("change", function () {
        $("[group='ftp']").closest("div").hide();
        $("[group='url']").closest("div").hide();
        if($(this).val() == 2)
            $("[group='ftp']").closest("div").show();
        if($(this).val() == 3)
            $("[group='url']").closest("div").show();
    }).change();
})

function ResetAllProducts(id, spKey) {
    Page.ShowConfirm(spKey + " value of all products will be reset. Are you sure you want to continue?",function () {
        Page.Ajax.SendBool("ResetAllProducts",id, function () {
            Page.ShowSuccess(spKey + " value of the products has been reset.");
        },"Processing...");
    });
}

/**
 * kaydetme sırasında kontrol ve değişiklikler
 * @param {Import} obj
 * @param {bool} returnValues 1: Sadece değerleri döndür, hata mesajlarını gözardı et, 0: Hata mesajları açık
 * @return {int} 1 ise sorun yok, kaydetmeye devam
 */
function ImportSaveFunc(obj, returnValues)
{
    let AttributesInfo = JsTable.GetList('Attributes');
    if(AttributesInfo == null)
        return 0;
    obj.AttributesInfo = AttributesInfo;
	return 1;
}
