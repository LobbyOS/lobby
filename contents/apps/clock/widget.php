<!DOCTYPE html>
<html>
  <head>
    <!--
      // Pixel polished jQuery & CSS3 Analogue Clock plugin V2.1
      // by molokoloco@gmail.com 10/10/2011 -
      // Infos : http://www.b2bweb.fr/molokoloco/pixels-polished-jquery-css3-analogue-clock/
      // jsFiddle : http://jsfiddle.net/molokoloco/V2rFN/

      // Used with permission from author (10/11/2011). -MH
    -->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <script type='text/javascript' src='<?echo L_JQUERY;?>'></script>

    <style type="text/css">
      /*
        // Pixel polished jQuery & CSS3 analogue clock
        // by molokoloco@gmail.com 08/10/2011

        //> Colors : easy to change, Sizes and positions : more tricky
        // Colors are based on a white semi-transparent theme :
        // replace "255,255,255" by "0,0,0" to blackify... or wathever you want
        // Mostly elements that need to be centered... are centered in JS,
        // So only kee in mind your SIZES ((---MOD---))
      */

      * { margin:0; padding:0; }

      /* Debug: div { border:1px solid rgba(0,0,0,0.5); } */

      /* ------- Clock container ------ */

      div#clock {
        position:relative; /* childs are abs. positionned */
        display:block;
        width:160px; height:160px; /* (---MOD---) */
        margin:2px auto; /* (---MOD---) or float left */
        border-radius:50%; /* (---MOD---) All round ? */
        background:black;
        transition:all 250ms ease-in-out; /* for mouseover effect */
        -webkit-transition:all 250ms ease-in-out;
        -moz-transition:all 250ms ease-in-out;
        -ms-transition:all 250ms ease-in-out;
        -o-transition:all 250ms ease-in-out;
        box-shadow:0 2px 6px rgba(0,0,0,0.3);
        -o-box-shadow:0 2px 6px rgba(0,0,0,0.3);
        -webkit-box-shadow:0 2px 6px rgba(0,0,0,0.3);
        -moz-box-shadow:0 2px 6px rgba(0,0,0,0.3);
        -ms-box-shadow:0 2px 6px rgba(0,0,0,0.3);
      }

      div.clockGlass { /* Give clock glass effect */
        position:absolute;
        z-index:6;
        top:0; left:0;
        right:0; bottom:0;
        border-radius:50%; /* (---MOD---) */
        /* online editor : http://www.colorzilla.com/gradient-editor/ */
        background:linear-gradient(top,rgba(255,255,255,0) 0%,rgba(255,255,255,0.2) 100%);
        background:-moz-linear-gradient(top,rgba(255,255,255,0) 0%,rgba(255,255,255,0.2) 100%);
        background:-webkit-gradient(linear,left top,left bottom,color-stop(0%,rgba(255,255,255,0)),color-stop(100%,rgba(255,255,255,0.2)));
        background:-webkit-linear-gradient(top,rgba(255,255,255,0) 0%,rgba(255,255,255,0.2) 100%);
        background:-o-linear-gradient(top,rgba(255,255,255,0) 0%,rgba(255,255,255,0.2) 100%);
        background:-ms-linear-gradient(top,rgba(255,255,255,0) 0%,rgba(255,255,255,0.2) 100%);
        filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#00000000',endColorstr='#29000000',GradientType=0 );
        box-shadow:inset 0 0 5px rgba(255,255,255,0.2),inset 5px 10px 10px rgba(255,255,255,0.2),inset -5px -10px 10px rgba(255,255,255,0.2);
        -o-box-shadow:inset 0 0 5px rgba(255,255,255,0.2),inset 5px 10px 10px rgba(255,255,255,0.2),inset -5px -10px 10px rgba(255,255,255,0.2);
        -webkit-box-shadow:inset 0 0 5px rgba(255,255,255,0.2),inset 5px 10px 10px rgba(255,255,255,0.2),inset -5px -10px 10px rgba(255,255,255,0.2);
        -moz-box-shadow:inset 0 0 5px rgba(255,255,255,0.2),inset 5px 10px 10px rgba(255,255,255,0.2),inset -5px -10px 10px rgba(255,255,255,0.2);
        -ms-box-shadow:inset 0 0 5px rgba(255,255,255,0.2),inset 5px 10px 10px rgba(255,255,255,0.2),inset -5px -10px 10px rgba(255,255,255,0.2);
      }

      /* ------- JS works with this elements ------ */

      div.innerCenter { /* Circle inside clock */
        position:absolute;
        z-index:2;
        top:50%; left:50%;
        width:127px; height:127px; /* (---MOD---) (Centered with margin by JS) */
        border-radius:50%; /* (---MOD---) */
        background:rgba(255,255,255,0.16);
        box-shadow:0 0 3px rgba(0,0,0,0.2),0 0 10px rgba(255,255,255,0.2);
        -moz-box-shadow:0 0 3px rgba(0,0,0,0.2),0 0 10px rgba(255,255,255,0.2);
        -webkit-box-shadow:0 0 3px rgba(0,0,0,0.2),0 0 10px rgba(255,255,255,0.2);
        -ms-box-shadow:0 0 3px rgba(0,0,0,0.2),0 0 10px rgba(255,255,255,0.2);
        -o-box-shadow:0 0 3px rgba(0,0,0,0.2),0 0 10px rgba(255,255,255,0.2);
      }

      div.sec,div.min,div.hour {
        position:absolute;
        /* transition:all 200ms linear; // smooth, but rotate anticlockwise from 359° to 1° */
      }
      div.sec {
        width:1px; /* (---MOD---) second clockwise width */
        z-index:10;
      }
      div.min {
        width:3px; /* (---MOD---) */
        z-index:11;
      }
      div.hour {
        width:5px; /* (---MOD---) */
        z-index:12;
      }

      div.sec  div.clockwise { top:24px; bottom: 75px; } /* (---MOD---) dist from clock border == clockwise height */
      div.min  div.clockwise { top:26px; bottom: 75px; } /* (---MOD---) */
      div.hour div.clockwise { top:50px; bottom: 75px; } /* (---MOD---) */

      div.clockwise {
        position:absolute;
        left:0; right:0;
        bottom:57px; /* (---MOD---) clockH/2 - 8 : center of the #clock, minus junction */
        background:#FFF;
        background:rgba(255,255,255,0.4);
        border-radius:2px;
        box-shadow:0 0 3px rgba(0,0,0,0.25);
        -moz-box-shadow:0 0 3px rgba(0,0,0,0.25);
        -webkit-box-shadow:0 0 3px rgba(0,0,0,0.25);
        -ms-box-shadow:0 0 3px rgba(0,0,0,0.25);
        -o-box-shadow:0 0 3px rgba(0,0,0,0.25);
      }

      div.digit, div.time, div.date {
        font-smoothing:antialiased; -webkit-font-smoothing:antialiased; -moz-font-smoothing:antialiased; -ms-font-smoothing:antialiased; -o-font-smoothing:antialiased;
        text-size-adjust:none; -webkit-text-size-adjust:none; -moz-text-size-adjust:none; -ms-text-size-adjust:none; -o-text-size-adjust:none;
      }

      div.digit {
        position:absolute;
        z-index:5;
        width:18px; height:18px;  /* (---MOD---) */
        font:18px/24px "Droid Sans", "Segoe UI", Arial, sans-serif; /* (---MOD---) */
        text-align:center;
        color:#FFF;
        color:rgba(255,255,255,0.33);
        text-shadow:0 0 2px rgba(255,255,255,0.25);
      }
      div.digit span { /* 12,3,6 and 9 */
        color:rgba(255,255,255,0.9);
      }

      div.unit {
        position:absolute;
        z-index:8;
        width:1px; height:4px; /* (---MOD---) */
        background:rgba(255,255,255,0.4);
      }

      div.time, div.date {
        position:absolute;
        z-index:5;
        top:82px; left:50%; /* (---MOD---) */
        width:200px; height:12px;  /* (---MOD---) */
        margin:0 0 0 -100px; /* (---MOD---) Centered with half this size - no JS */
        text-align:center;
        font:bold 11px/12px 'Courrier New','Courier',serif; /* (---MOD---) */
        text-shadow:0 0 3px rgba(255, 255, 255, 0.5);
        color:#000;
        color:rgba(0,0,0,0.3);
      }
      div.date { top:70px; } /* (---MOD---) */

    </style>

    <script type="text/javascript" src="<?echo CUR_APP;?>/clock.js"></script>
  </head>

  <body>
    <div id="clock">
      <div class="clockGlass"></div>
    </div>
  </body>
</html>
