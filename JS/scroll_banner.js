var bNetscape4plus = (navigator.appName == "Netscape" && navigator.appVersion.substring(0,1) >= "4");
var bExplorer4plus = (navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.substring(0,1) >= "4");
function CheckUIElements(){
     var yMenuFrom, yMenuTo, yButtonFrom, yButtonTo, yOffset, timeoutNextCheck;

     if ( bNetscape4plus ) {
             yMenuFrom   = document["divBanner"].top;
             yMenuTo     = top.pageYOffset + 200; //�ݽ��������� ���� ���̾� ��ǥ ��
     }
     else if ( bExplorer4plus ) {
             yMenuFrom   = parseInt (divBanner.style.top, 10);
             yMenuTo     = document.body.scrollTop + 450; //�ͽ��÷η��� ���� ���̾� ��ǥ ��
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
             document["divBanner"].top = top.pageYOffset + 135; //�ݽ��������� �ε��� ���� ���̾� ��ǥ ��
             document["divBanner"].visibility = "visible";
     }
     else if ( bExplorer4plus ) {
             divBanner.style.top = document.body.scrollTop + 200; //�ͽ��÷η��� �ε��� ���� ���̾� ��ǥ ��
             divBanner.style.visibility = "visible";
     }
     CheckUIElements();
     return true;
}
