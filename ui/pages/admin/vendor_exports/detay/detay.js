let lastOperator= "IF";
$(function () {
    $("a[tab-title]").on("click",function () {
        window.history.pushState(null,"",Page.UrlChangeParam($(this).attr("tab-title"),$(this).attr("tab-name")));
        // window.location.href = Page.UrlChangeParam("aktif_tab","rules");
    });
    if(Page.GetParameter("aktif_general_tab") != "")
        $("a[href='#"+Page.GetParameter("aktif_general_tab")+"']").click();
    if(Page.GetParameter("aktif_rule_tab") != "")
        $("a[href='#"+Page.GetParameter("aktif_rule_tab")+"']").click();
    var FiltersModal = {
        Fields: {
            Operator: {D: 'Operator',T:"text",Attr: {readonly: "1",value: "IF"}},
            PAttributeId: {D: 'Field',T:attrs,CS: 1, RQ: 1, Attr: {value: "", "editable_list" : 1}},
            ConditionRule: {D: 'Rule',T:{1: "EQ", 2: "Not EQ", 3: "Greater", 4: "Greater EQ", 5: "Less", 6: "Less EQ", 7: "Contains"},CS: 1,Attr: {value: "", "editable_list" : 1}},
            Value: {D: 'Value',T:'textarea',CS: 1,Attr: {"placeholder": ""}},
            // BelgeTarihi: {D: 'Belge Tarihi',T:'date',CS: 1},
            // ParaBirimi: {D: 'Para Birimi',T:{1: "TL", 2: "Dolar", 3: "Euro"},RQ: 1,CS: 1},
            // Fiyat: {D: 'Fiyat',T:'money',CS: 1,Attr: {"unit": " "}},
        },
        // MinRowCount: 1,
        OnBeforeShow: function (data) {
            data = {Operator: lastOperator};
            return data;
            // var select = $('select[field="DogruCevap"]');
            // var options = select.find('option');
            //
            // options.sort(function(a, b) {
            //     return $(a).text().localeCompare($(b).text());
            // });
            // select.html(options);
        },
        OnUpdate: function (tr,div) {
            // var select = $('select[field="DogruCevap"]');
            // var options = select.find('option');
            //
            // options.sort(function(a, b) {
            //     return $(a).text().localeCompare($(b).text());
            // });
            // select.html(options);
        },
        ExtraRowButtons: [
            "<button class='btn btn-primary btn-sm add-and-row' onclick='YeniSatirEkle(this,1)'><i class='fa fa-plus-circle'></i> AND</button>",
            "<button class='btn btn-primary btn-sm add-or-row' onclick='YeniSatirEkle(this,0)'><i class='fa fa-plus-circle'></i> OR</button>",
        ],
        RowAttributes: [
            "Operator"
        ],
        Dialog: {C: 'Ekle', W: 500, H: 320},
        // RowTemplate: "Deneme",
        MinRowCount: 1,
        Insertable: true,
        // Sortable: true,
        Deletable:  true,
        DetayForm:  false,
        InlineEdit: true,
        RowNumbers: true
    };
    var TransactionsModal = {
        Fields: {
            // Operator: {D: 'Operator',T:"text",Attr: {readonly: "1",value: "IF"}},
            PAttributeId: {D: 'Field',T:attrs,CS: 1, RQ: 1, Attr: {value: "", "editable_list" : 1}},
            ConditionRule: {D: 'Rule',T:{1: "set value",2: "set formula"},CS: 1,Attr: {value: "", "editable_list" : 1}},
            Value: {D: 'Value',T:'textarea',CS: 1,Attr: {"placeholder": ""}},
            // BelgeTarihi: {D: 'Belge Tarihi',T:'date',CS: 1},
            // ParaBirimi: {D: 'Para Birimi',T:{1: "TL", 2: "Dolar", 3: "Euro"},RQ: 1,CS: 1},
            // Fiyat: {D: 'Fiyat',T:'money',CS: 1,Attr: {"unit": " "}},
        },
        // MinRowCount: 1,
        OnBeforeShow: function (data) {
            data = {};
            return data;
            // var select = $('select[field="DogruCevap"]');
            // var options = select.find('option');
            //
            // options.sort(function(a, b) {
            //     return $(a).text().localeCompare($(b).text());
            // });
            // select.html(options);
        },
        Dialog: {C: 'Ekle', W: 500, H: 320},
        Insertable: true,
        Deletable:  true,
        DetayForm:  false,
        InlineEdit: true,
        RowNumbers: true
    };
    Object.entries(RulesInfo).forEach(([key, rule]) => {
        JsTable.Init('Filters_' + rule.Id, FiltersModal, rule.FiltersInfo);
        Jui.InitInputs("#Filters_" + rule.Id);
        JsTable.Init('Transactions_' + rule.Id, TransactionsModal, rule.TransactionsInfo);
        Jui.InitInputs("#Transactions_" + rule.Id);
    });

    $("#rules-menu").sortable({
        start: function(event, ui) {
            // Başlangıç sıralamasını kaydet (eski hali)
            initialOrder = $("#rules-menu").html();
        },
        stop: function(event, ui) {
            // Kullanıcıya onay sorusu sor
            var isConfirmed = confirm("Kaydetmek istediğinize emin misiniz?");

            if (!isConfirmed) {
                // Eğer kullanıcı onaylamazsa, eski sıralamaya geri dön
                $("#rules-menu").html(initialOrder);
            }
        }
    });

    var $draggedItem, $stickyItem, itemWidth, itemHeight;
    $(".filter-list-content").find("table tbody").sortable({
        start: function(event, ui) {
            $draggedItem = $(ui.item[0]);
            //Store the constant width and height values in variables.
            itemWidth = $draggedItem.width();
            itemHeight = $draggedItem.height();
            //next is called twice because a hidden "tr" is added when sorting.
            // var $nextChild = $(ui.item[0]).next().next();

            // Tüm $stickyItems dizisini boşaltıyoruz
            $stickyItems = [];

            // Sürüklenen satırdan itibaren ardışık OR olan satırları buluyoruz
            var $nextChild = $(ui.item[0]).next().next();

            while ($nextChild.attr('operator') === "OR") {
                $stickyItems.push($nextChild);  // operator="OR" olan satırları $stickyItems dizisine ekle
                $nextChild = $nextChild.next();  // Sonraki satıra geç
            }
        },
        sort: function() {
            if ($stickyItems.length > 0) {
                // Her bir sticky item'i sürüklenen satıra göre yeniden konumlandırıyoruz
                $stickyItems.forEach(function($stickyItem, index) {
                    $stickyItem.css({
                        "z-index": $draggedItem.css('z-index'),
                        "position": "absolute",
                        "width": itemWidth,
                        "height": itemHeight,
                        "left": $draggedItem.css('left'),
                        "top": $draggedItem.position().top + (itemHeight * (index + 1)) // Alt alta dizmek için itemHeight eklenir
                    });
                });
            }
        },
        stop: function() {
            if ($stickyItems.length > 0) {
                // Her bir sticky item'i sürüklenen satırın arkasına sırayla ekliyoruz
                $stickyItems.forEach(function($stickyItem) {
                    $stickyItem.insertAfter($draggedItem);
                    $draggedItem = $stickyItem;  // Sonra taşınan item yeni referans olur
                    $stickyItem.removeAttr('style');  // Stil sıfırlaması yap
                });
            }
        }
    });

    $(".RuleTransactionSelect").on("change",function () {
        let ruleId = $(this).attr("rule_id");
        if($(this).val() == 0)
            $(".transaction-list-content[rule_id='"+ruleId+"']").show();
        else
            $(".transaction-list-content[rule_id='"+ruleId+"']").hide();
    }).change();
})

/**
 * kaydetme sırasında kontrol ve değişiklikler
 * @param {VendorExport} obj
 * @param {bool} returnValues 1: Sadece değerleri döndür, hata mesajlarını gözardı et, 0: Hata mesajları açık
 * @return {int} 1 ise sorun yok, kaydetmeye devam
 */
function VendorExportSaveFunc(obj, returnValues)
{

    // let FiltersInfo = JsTable.GetList('Filters');
    // if(FiltersInfo == null)
    //     return 0;
    // obj.FiltersInfo = FiltersInfo;
	return 1;
}

function ShowAddRule()
{
    Page.ShowDialogBS("AddRuleDiv",0,0,function () {
        let obj = {};
        obj.Name = $("#AddRuleDiv_Name").val();
        Page.Ajax.Send("SaveNewRule",obj, function (resp) {
            if(resp == "Export not found")
            {
                Page.ShowError("Export not found");
                return;
            }
            window.history.pushState(null,"",Page.UrlChangeParam("aktif_rule_tab","rule_" + resp));
            Page.Refresh();
        });
        return 1;
    });
}

function SaveRule(id, sum)
{
    Page.ShowConfirm("Kaydetmek istediğinize emin misiniz?",function () {
        let obj = {};
        obj.Id = id;
        obj.Name = $("#RuleName_" + id).val();
        obj.Transaction = $("#RuleTransaction_" + id).val();

        let TransactionsInfo = JsTable.GetList('Transactions_' + id);
        let FiltersInfo = JsTable.GetList('Filters_' + id);
        if(FiltersInfo == null)
            return 0;
        if(TransactionsInfo == null)
            return 0;
        FiltersInfo.forEach(function (item, key) {
            FiltersInfo[key].FilterSort = key + 1;
        });
        obj.FiltersInfo = FiltersInfo;
        obj.TransactionsInfo = TransactionsInfo;
        Page.Ajax.SendBool("SaveRule",obj, function () {
            if(! sum)
                Page.Refresh();
            else
            {
                Page.Ajax.SendJson("GetRuleSummary",id,function (resp) {
                    $("div.rule-summary-" + id).find(".total-count").html("Total: " + resp.Total);
                    $("div.rule-summary-" + id).find(".before-count").html("Before: " + resp.Before);
                    $("div.rule-summary-" + id).find(".after-count").html("After: " + resp.After);
                },"Calculating...");
            }
        });
    });
}

function DeleteRule(id)
{
    Page.ShowConfirm("Silmek istediğinize emin misiniz?",function () {
        Page.Ajax.SendBool("DeleteRule",id, function () {
            Page.Refresh();
        });
    });
}

function ChangeStatusRule(id)
{
    Page.ShowConfirm("Durumunu değiştirmek istediğinize emin misiniz?",function () {
        Page.Ajax.SendBool("ChangeStatusRule",id, function () {
            Page.Refresh();
        });
    });
}

function YeniSatirEkle(obj,tur) {
    if(tur)
        lastOperator = "IF";
    else
        lastOperator = "OR";
    let divId = $(obj).closest("div.has_js_table").attr("id");
    $("button[div_id='#"+divId+"']").click();
    let firstTr = $(obj).closest("tr");
    let lastTr = $(obj).closest("table").find("tbody").find("tr:last");
    if(lastOperator == "OR")
        firstTr.after(lastTr);

    // alert($(btnObj).closest("tr").find("td.SiraNo").text());
}
