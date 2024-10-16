<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="javascript:;">
            <div class="visual">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{$KullaniciSayisi}">{$KullaniciSayisi}</span>
                </div>
                <div class="desc"> Total Users </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="javascript:;">
            <div class="visual">
                <i class="fa fa-eye"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{$ToplamGoruntulenme}">{$ToplamGoruntulenme|number_format}</span>
                </div>
                <div class="desc"> Total  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="javascript:;">
            <div class="visual">
                <i class="fa fa-address-card-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{$VendorCount}">{$VendorCount|number_format}</span>
                </div>
                <div class="desc"> Vendor Count  </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 purple" href="javascript:;">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{$FirmaSayisi}">{$FirmaSayisi}</span>
                </div>
                <div class="desc"> Company Count </div>
            </div>
        </a>
    </div>
</div>

{*<div class="row">
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bubble font-dark hide"></i>
                    <span class="caption-subject font-hide bold uppercase">En Çok Görüntülenenler</span>
                </div>
                *}{*<div class="actions">
                    <div class="btn-group">
                        <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Actions
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;"> Option 1</a>
                            </li>
                            <li class="divider"> </li>
                            <li>
                                <a href="javascript:;">Option 2</a>
                            </li>
                            <li>
                                <a href="javascript:;">Option 3</a>
                            </li>
                            <li>
                                <a href="javascript:;">Option 4</a>
                            </li>
                        </ul>
                    </div>
                </div>*}{*
            </div>
            <div class="portlet-body">
                <div class="row">
                    {foreach $EnCokGoruntulenenKullanicilar as $ecgKullanici}
                        <div class="col-md-4">
                            <!--begin: widget 1-1 -->
                            <div class="mt-widget-1">
                                *}{*<div class="mt-icon">
                                    <a href="#">
                                        <i class="icon-plus"></i>
                                    </a>
                                </div>*}{*
                                <div class="mt-img">
                                    <img src="{$ecgKullanici->LogoTamYol}"> </div>
                                <div class="mt-body">
                                    <h3 class="mt-username">{$ecgKullanici->AdSoyad}</h3>
                                    <p class="mt-user-title"> Profil Görüntülenmesi<br><b>{$ecgKullanici->GoruntulenmeSayisi}</b><br>Rehbere Eklenme<br><b>{$ecgKullanici->KaydedilmeSayisi}</b></p>
                                    <p class="mt-user-title" style="word-break: break-word;"> <b>{$ecgKullanici->FirmaAdsStr}</b> </p>
                                    <div class="mt-stats">
                                        <div class="btn-group btn-group btn-group-justified">
                                            <a href="javascript:;" class="btn font-red">
                                                <i class="icon-bubbles"></i>
                                            </a>
                                            <a href="{$SITE_URL}profile/{$ecgKullanici->ProfileId}" target="_blank" class="btn font-green">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            *}{*<a href="javascript:;" class="btn font-yellow">
                                                <i class="icon-emoticon-smile"></i>
                                            </a>*}{*
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end: widget 1-1 -->
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Son 10 Görüntülenme</span>
                </div>
                *}{*<div class="actions">
                    <div class="btn-group">
                        <a class="btn btn-sm blue btn-outline btn-circle" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> Filter By
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <div class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
                            <label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" /> Finance
                                <span></span>
                            </label>
                            <label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" checked="" /> Membership
                                <span></span>
                            </label>
                            <label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" /> Customer Support
                                <span></span>
                            </label>
                            <label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" checked="" /> HR
                                <span></span>
                            </label>
                            <label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" /> System
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>*}{*
            </div>
            <div class="portlet-body">
                <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
                    <ul class="feeds">
                        {foreach $SonAktiviteler as $aktivite}
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-eye"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc">
                                                <b>{$aktivite['ad_soyad']}</b> adlı kartvizit görüntülendi
                                                *}{*<span class="label label-sm label-warning ">
                                                    Take action
                                                    <i class="fa fa-share"></i>
                                                </span>*}{*
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> {Tarih::TahminiFarkStr($aktivite['son_goruntulenme'])} </div>
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
                *}{*<div class="scroller-footer">
                    <div class="btn-arrow-link pull-right">
                        <a href="javascript:;">See All Records</a>
                        <i class="icon-arrow-right"></i>
                    </div>
                </div>*}{*
            </div>
        </div>
    </div>
</div>*}
