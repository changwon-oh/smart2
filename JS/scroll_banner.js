var bNetscape4plus = (navigator.appName == "Netscape" && navigator.appVersion.substring(0,1) >= "4");
var bExplorer4plus = (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.substring(0,1) >= "4");
function CheckUIElements(){
     var yMenuFrom, yMenuTo, yButtonFrom, yButtonTo, yOffset, timeoutNextCheck;

     if ( bNetscape4plus ) {
             yMenuFrom   = document["divBanner"].top;
             yMenuTo     = top.pageYOffset + 200; //넷스케이프용 최초 레이어 좌표 값
     }
     else if ( bExplorer4plus ) {
             yMenuFrom   = parseInt (divBanner.style.top, 10);
             yMenuTo     = document.body.scrollTop + 450; //익스플로러용 최초 레이어 좌표 값
     }

     timeoutNextCheck = 500;

     if ( Math.abs (yButtonFrom - (yMenuTo + 152)) < 6 && yButtonTo < yButtonFrom ) {
             setTimeout ("CheckUIElements()", timeoutNextCheck);
             return;
     }

     if ( yButtonFrom != yButtonTo ) {
             yOffset = Math.ceil( Math.abs( yButtonTo - yButtonFrom ) / 1 );
             if ( yButtonTo < yButtonFrom )
                     yOffset = -yOffset;

             if ( bNetscape4plus )
                     document["divLinkButton"].top += yOffset;
             else if ( bExplorer4plus )
                     divLinkButton.style.top = parseInt (divLinkButton.style.top, 10) + yOffset;

             timeoutNextCheck = 10;
     }
     if ( yMenuFrom != yMenuTo ) {
             yOffset = Math.ceil( Math.abs( yMenuTo - yMenuFrom ) / 1 );
             if ( yMenuTo < yMenuFrom )
                     yOffset = -yOffset;

             if ( bNetscape4plus )
                     document["divBanner"].top += yOffset;
             else if ( bExplorer4plus )
                     divBanner.style.top = parseInt (divBanner.style.top, 10) + yOffset;

             timeoutNextCheck = 10;
     }

     setTimeout ("CheckUIElements()", timeoutNextCheck);
}

function scroll_banner()
{
     var y;
     if ( bNetscape4plus ) {
             document["divBanner"].top = top.pageYOffset + 135; //넷스케이프용 로딩시 시작 레이어 좌표 값
             document["divBanner"].visibility = "visible";
     }
     else if ( bExplorer4plus ) {
             divBanner.style.top = document.body.scrollTop + 200; //익스플로러용 로딩시 시작 레이어 좌표 값
             divBanner.style.visibility = "visible";
     }
     CheckUIElements();
     return true;
}
