<DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      body {
        margin: 0;
        margin-top: 10px;
        padding: 0;
        user-select: none;
        -webkit-user-select: none;
        width: 406px;

        font-family: "Segoe UI", "Droid Sans", Arial, sans-serif;

        background-image: linear-gradient(right bottom, rgb(121,209,121) 0%, rgb(5,135,5) 100%);
        background-image: -o-linear-gradient(right bottom, rgb(121,209,121) 0%, rgb(5,135,5) 100%);
        background-image: -moz-linear-gradient(right bottom, rgb(121,209,121) 0%, rgb(5,135,5) 100%);
        background-image: -webkit-linear-gradient(right bottom, rgb(121,209,121) 0%, rgb(5,135,5) 100%);
        background-image: -ms-linear-gradient(right bottom, rgb(121,209,121) 0%, rgb(5,135,5) 100%);

        background-image: -webkit-gradient(
          linear,
          right bottom,
          left top,
          color-stop(0, rgb(121,209,121)),
          color-stop(1, rgb(5,135,5))
        );
      }
      .qtip.qtip-dark {
        width: auto !important;
      }
      #google {
        width: 100%;
        height: 93%;
        background-image: url(<?echo CUR_APP;?>/widget.google.png);
        background-repeat: no-repeat;
        background-position: center center;
        z-index: 5;
      }
      a {
        text-decoration: none;
      }
      #services {
        position: absolute;
        height: 35px;
        left: 0px;
        right: 0px;
        text-align: center;
        padding-top: 2px;
        padding-bottom: 2px;
      }
      .s-top {
        top: 0;
      }
      .s-bottom {
        bottom: 0;
      }
      .SVC {
        display: inline-block;
        height: 35px;
        width: 35px;
        opacity: .77;

        background-image: url(<?echo CUR_APP;?>/widget.google.products.png);
        background-color: transparent;
        background-repeat: no-repeat;
      }
      .SVC:hover {
        opacity: 1;
      }
      #PLUS {
        background-position: -462px 0;
      }
      #GWS {
        background-position: -762px 0;
      }
      #IMG {
        background-position: -126px 0;
      }
      #GMAIL {
        background-position: -168px 0;
      }
      #VOICE {
        background-position: -678px 0;
      }
      #YT {
        background-position: -804px 0;
      }
      #DRIVE {
        background-position: -42px 0;
      }
      #CAL {
        background-position: 0 0;
      }
      #MAPS {
        background-position: -210px 0;
      }
      #READER {
        background-position: -636px 0;
      }
      #NEWS {
        background-position: -294px 0;
      }
      #TRANSLATE {
        background-position: -594px 0;
      }
      #MUSIC {
        background-position: -252px 0;
      }
      #BMKS {
        background-position: -546px 0;
      }
      #PICASA {
        background-position: -378px 0;
      }
      #FINANCE {
        background-position: -84px 0;
      }
      #PROD {
        background-position: -504px 0;
      }
      #OFFERS {
        background-position: -336px 0;
      }
      #WALLET {
        background-position: -720px 0;
      }
      #PLAY {
        background-position: -420px 0;
      }
    </style>
  </head>
  <body>
    <a href="http://www.google.com/" target="_block"><div id="google">&nbsp;</div></a>
    <div id="services" class="s-top">
      <a class="SVC" id="PLUS"      href="https://plus.google.com/"           title="Google Plus"             target="_block">&nbsp;</a>
      <a class="SVC" id="GWS"       href="https://www.google.com/"            title="Google.com"              target="_block">&nbsp;</a>
      <a class="SVC" id="IMG"       href="https://www.google.com/imghp"       title="Google Image Search"     target="_block">&nbsp;</a>
      <a class="SVC" id="MAPS"      href="https://www.google.com/maps"        title="Google Maps"             target="_block">&nbsp;</a>
      <a class="SVC" id="NEWS"      href="https://news.google.com"            title="Google News"             target="_block">&nbsp;</a>
      <a class="SVC" id="READER"    href="https://reader.google.com/"         title="Google Reader"           target="_block">&nbsp;</a>
      <a class="SVC" id="FINANCE"   href="https://www.google.com/finance"     title="Google Finance"          target="_block">&nbsp;</a>
      <a class="SVC" id="PROD"      href="https://www.google.com/products"    title="Google Product Search"   target="_block">&nbsp;</a>
      <a class="SVC" id="BMKS"      href="https://www.google.com/bookmarks"   title="Google Bookmarks"        target="_block">&nbsp;</a>
      <a class="SVC" id="TRANSLATE" href="https://www.google.com/translate"   title="Google Translate"        target="_block">&nbsp;</a>
    </div>

    <div id="services" class="s-bottom">
      <a class="SVC" id="GMAIL"     href="https://mail.google.com/"           title="Gmail"                   target="_block">&nbsp;</a>
      <a class="SVC" id="DRIVE"     href="https://drive.google.com/"          title="Google Drive"            target="_block">&nbsp;</a>
      <a class="SVC" id="VOICE"     href="https://www.google.com/voice/"      title="Google Voice"            target="_block">&nbsp;</a>
      <a class="SVC" id="PLAY"      href="https://play.google.com/"           title="Google Play"             target="_block">&nbsp;</a>
      <a class="SVC" id="MUSIC"     href="https://music.google.com/"          title="Google Play Music"       target="_block">&nbsp;</a>
      <a class="SVC" id="YT"        href="https://www.youtube.com/"           title="YouTube"                 target="_block">&nbsp;</a>
      <a class="SVC" id="PICASA"    href="https://picasaweb.google.com/"      title="Picasa"                  target="_block">&nbsp;</a>
      <a class="SVC" id="CAL"       href="https://www.google.com/calendar/"   title="Google Calendar"         target="_block">&nbsp;</a>
      <a class="SVC" id="OFFERS"    href="https://www.google.com/offers/"     title="Google Offers"           target="_block">&nbsp;</a>
      <a class="SVC" id="WALLET"    href="https://wallet.google.com/manage/"  title="Google Wallet"           target="_block">&nbsp;</a>
    </div>
  </body>
</html>
