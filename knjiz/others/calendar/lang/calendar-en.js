// ** I18N

// Calendar EN language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Pazar",
 "Pazartesi",
 "Salı",
 "Çarşamba",
 "Perşembe",
 "Cuma",
 "Cumartesi",
 "Pazar");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Paz",
 "Pzt",
 "Sal",
 "Çar",
 "Per",
 "Cum",
 "Cmt",
 "Paz");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("Ocak",
 "Şubat",
 "Mart",
 "Nisan",
 "Mayıs",
 "Haziran",
 "Temmuz",
 "Ağustos",
 "Eylül",
 "Ekim",
 "Kasım",
 "Aralık");

// short month names
Calendar._SMN = new Array
("Oca",
 "Şub",
 "Mar",
 "Nis",
 "May",
 "Haz",
 "Tem",
 "Ağu",
 "Eyl",
 "Eki",
 "Kas",
 "Ara");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "Takvim hakkında";

Calendar._TT["ABOUT"] =
"DHTML Tarih Seçici\n" +
"Orjinal yazılım : " +
"(c) dynarch.com 2002-2005 / Yazan: Mihai Bazon\n" + // don't translate this this ;-)
"Çeviri ve Uyarlama : DOĞRU\n"
"\n\n" ;
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Zaman seçimi:\n" +
"- Herhangi bir zaman bölümüne tıklarayak artırınız\n" +
"- veya Shift+tıklama ile azaltınız\n" +
"- veya sürükleme yaparak daha hızlı artırım azaltım yapınız.";

Calendar._TT["PREV_YEAR"] = "Önceki yıl(Liste için basılı tutun)";
Calendar._TT["PREV_MONTH"] = "Önceki ay(Liste için basılı tutun)";
Calendar._TT["GO_TODAY"] = "Bugünü seç";
Calendar._TT["NEXT_MONTH"] = "Sonraki ay(Liste için basılı tutun)";
Calendar._TT["NEXT_YEAR"] = "Sonraki ay(Liste için basılı tutun)";
Calendar._TT["SEL_DATE"] = "Tarih seç";
Calendar._TT["DRAG_TO_MOVE"] = "Hareket için sürükle";
Calendar._TT["PART_TODAY"] = " (bugün)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "%s yi önce görüntüle";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Kapat";
Calendar._TT["TODAY"] = "Bugün";
Calendar._TT["TIME_PART"] = "(Shift ile) tıklayarak değiştiriniz";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "Hafta";
Calendar._TT["TIME"] = "Saat:";
