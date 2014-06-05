var ESLIP = function(){

    var PLUGIN_SCRIPT = getPluginScript();
    var PLUGIN_URL = getPluginURL();

    // obtener el tag script correspondiente al plugin
    function getPluginScript(){
        var scripts = document.getElementsByTagName('script');
        var pluginScript;
        if(scripts && scripts.length>0) {
            for(var i in scripts) {
                if(scripts[i].src && scripts[i].src.match(/\/eslip_plugin\.js$/)) {
                    pluginScript = scripts[i];
                    break;
                }
            }
        }
        return pluginScript;
    }
    
    // obtener la url del plugin a partir del tag script
    function getPluginURL(){
        var path = PLUGIN_SCRIPT.src.replace(/(.*)\/\eslip_plugin.js$/, '$1');
        path = path.substr(0, path.lastIndexOf( '/' )+1);
        return path;
    }

    // verifica si el plugin debe iniciarse automaticamente
    // este dato se obtiene a partir del atributo autoInit del tag script
    function isAutoInit(){
        var autoInit = 'true';
        if (PLUGIN_SCRIPT && typeof PLUGIN_SCRIPT !== 'undefined'){
            autoInit = PLUGIN_SCRIPT.getAttribute("autoInit") || 'true';
        }
        return autoInit;
    }

    // crea el objeto para realizar una llamada ajax
    function createHTTPObject(){
        var xmlhttp;
        if (window.ActiveXObject){
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }else{
            if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
            }
        }
        return xmlhttp;
    }

    // realiza la inicializacion del widget
    function initWidget(){
        if (typeof document.getElementById("ESLIP_Plugin") !== 'undefined'){
            var ajax = createHTTPObject();
            ajax.open("GET", PLUGIN_URL+"eslip_frontend_services/getWidgetData", true);
            ajax.onreadystatechange = function(){ 
                if (ajax.readyState == 4){
                    var response = ajax.responseText;
                    var data = JSON.parse(ajax.responseText);
                    renderWidget(data);
                }
            }
            ajax.send();
        }
    }

    // retorna el html de un boton de login, creado a partir de un objeto button
    function buttonStyleElement(button){
        var id = button.id;
        var label = button.label || '';
        var labelText = label;
        var logo = 'background-image: url('+ button.logo +');' || '';
        var backgroundColor =  'background-color: ' + button.backgroundColor + ';' || '';
        var color = 'color: '+ button.textColor + ';' || '';
        var width = 'width: '+button.width+'%;';

        var onclick = 'ESLIP.clickLogin(\'' + id + '\')';

        if (!button.showLabel){
            labelText = '';
            backgroundColor = '';
        }

        var style = backgroundColor + color + width;

        var buttonHtml = '' +
            '<a href="javascript:;" class="eslip_button eslip_'+id+'" style="'+style+'" title="'+label+'" onclick="'+onclick+'">' +
                '<span class="eslip_button_image" style="'+logo+'" title="'+label+'"></span>' +
                '<span class="eslip_button_label">'+labelText+'</span>' +
            '</a>'
        ;

        return buttonHtml;
    }

    // crea un objeto button y lo retorna
    function newButton(idProvider, buttonWidth, showLabel){
        return {
            id: idProvider.id,
            label: idProvider.label || '',
            logo: idProvider.styles.logo_url,
            backgroundColor: idProvider.styles.backgroundColor,
            textColor: idProvider.styles.textColor,
            width: buttonWidth,
            showLabel: showLabel
        };
    }

    // renderizado del widget de login en el elemtno correspondiente al plugin
    function renderWidget(data){
        var rows = data.loginWidget.widgetRows;
        var columns = data.loginWidget.widgetColumns;
        var widgetWidth = data.loginWidget.widgetWidth;
        var showLabel = parseInt(data.loginWidget.buttonLabel);

        var buttonsPerSlide;
        var slides;
        var buttonWidth = Math.floor((100-(columns-1))/columns);

        if( (rows * columns)  <= 0){
            buttonsPerSlide = data.identityProviders.length;
            slides = 1;
        }else{
            buttonsPerSlide = rows * columns;
            slides = Math.ceil( data.identityProviders.length / buttonsPerSlide);
        }

        // ancho del widget
        document.getElementById("ESLIP_Plugin").style.width = widgetWidth;
        document.getElementById("ESLIP_Plugin").style.height = 'auto';

        var widgetHTML = ''

        var buttonPrev = '<div id="eslip_slideleft" class="eslip_prevArrow" onclick="slideshow.move(-1)"></div>';
        var buttonNext = '<div id="eslip_slideright" class="eslip_nextArrow" onclick="slideshow.move(1)"></div>';

        widgetHTML = '';

        if (slides > 1){
            widgetHTML += buttonPrev;
        }

        var viewStyle = (showLabel) ? 'buttonView' : 'iconView';  

        widgetHTML += '<div id="eslip_wraper" class="'+viewStyle+'">'+
                        '<ul>'
        ;
        
        var globalIndex = 0;
        var idProvider;
        var button;
        for (var i = 0; i < slides; i++){
            
            widgetHTML+= '<li>';

            for (var j = 0; j < buttonsPerSlide; j++){

                if( typeof data.identityProviders[globalIndex] !== 'undefined'){

                    idProvider = data.identityProviders[globalIndex];
                    button = newButton(idProvider, buttonWidth, showLabel);
                    widgetHTML += buttonStyleElement(button);

                }

                globalIndex++;
            }

             widgetHTML += '</li>';
        }

        widgetHTML += ''+
                    '</ul>' +
                '</div>'
        ;

        if (slides > 1){
            widgetHTML += buttonNext;
        }

        document.getElementById("ESLIP_Plugin").innerHTML = widgetHTML;

        if(!showLabel){
            var as = document.getElementById("eslip_wraper").getElementsByTagName('a');
            for (var i = as.length - 1; i >= 0; i--) {
                var buttonWidth = as[i].clientWidth;
                var paddingLeft = parseInt(window.getComputedStyle(as[i], null).getPropertyValue("padding-left"));
                var iconWidth = as[i].getElementsByTagName('span')[0].offsetWidth;

                as[i].getElementsByTagName('span')[0].style.marginLeft = ((buttonWidth/2)-paddingLeft-(iconWidth/2)) + "px";
            };
        }
        
        if (slides > 1){

            var slideleftWidth = document.getElementById("eslip_slideleft").offsetWidth;
            var sliderightWidth = document.getElementById("eslip_slideright").offsetWidth;    
            
            var widgetWidthInt = document.getElementById("ESLIP_Plugin").offsetWidth;

            var sliderWidth = widgetWidthInt-slideleftWidth-sliderightWidth-1; // -1 Fix para cuando el ancho del widget es %

            document.getElementById("eslip_wraper").style.width = sliderWidth+'px';

            document.getElementById("ESLIP_Plugin").getElementsByTagName('ul')[0].className = 'slider';

            var lis = document.getElementById("ESLIP_Plugin").getElementsByTagName('li');

            for (var i = lis.length - 1; i >= 0; i--) {
                lis[i].style.width = sliderWidth+'px';
            };

            var sliderHeight = document.getElementById("ESLIP_Plugin").getElementsByTagName('li')[0].clientHeight;

            document.getElementById("ESLIP_Plugin").style.height = sliderHeight+'px';

            document.getElementById("eslip_wraper").style.height = sliderHeight+'px';

            var arrowMarginTop = (sliderHeight/2)-(document.getElementById("eslip_slideleft").offsetHeight / 2);

            document.getElementById("eslip_slideleft").style.marginTop = arrowMarginTop+'px';

            document.getElementById("eslip_slideright").style.marginTop = arrowMarginTop+'px';

            setTimeout(function(){
                window.slideshow = new widgetSlider.slide('slideshow',{
                    id:'eslip_wraper',
                    auto:false, // false for no automation else the number of seconds between slides
                    resume:false, // continue auto sliding after interruption
                    vertical:false,
                    activeclass:'eslip_current', // active class for pagination
                    position:0, // initial position index
                    rewind:false, // toggle "rewinding", else the slides will be continuous
                    elastic:true, // toggle the bouncing effect of the slides
                    left:'eslip_slideleft', // ID of left nav, to cancel cursor selection
                    right:'eslip_slideright' // ID of left nav, to cancel cursor selection
                });
            }, 100);
        }
    }

    // evento click de los botones de login
    function clickLogin(server){
        var referer = window.location.href;
        if (server == 'openid'){
            var url = PLUGIN_URL+"eslip_openid.php?referer="+encodeURIComponent(referer);
        }
        else{
            var url = PLUGIN_URL+"eslip_oauth.php?server="+server+"&referer="+encodeURIComponent(referer);    
        }
        openLoginWindow(url);
    }

    // abre el popup de login
    function openLoginWindow(url){
        var newwindow;
        var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
             screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
             outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
             outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
             width    = 500,
             height   = 270,
             left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
             top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
             features = (
                'width=' + width +
                ',height=' + height +
                ',left=' + left +
                ',top=' + top
              );
        newwindow = window.open(url,'ESLIP',features);
        if (window.focus){
            newwindow.focus();
        }
        return false;
    }

    // slider del widget de login
    var widgetSlider = function(){
        
        function slide(n,p){
            this.n = n;
            this.init(p);
        }

        slide.prototype.init=function(p){
            var s = this.x = document.getElementById(p.id);
            var u = this.u = s.getElementsByTagName('ul')[0];
            var c = this.m = u.getElementsByTagName('li');
            var l = c.length;
            var i = this.l = this.c = 0;
            this.b = 1;

            if(p.navid&&p.activeclass){
                this.g = document.getElementById(p.navid).getElementsByTagName('li');
                this.s = p.activeclass;
            }
            this.a=p.auto||0; this.p=p.resume||0; this.r=p.rewind||0; this.e=p.elastic||false; this.v=p.vertical||0; s.style.overflow='hidden';
            for(i;i<l;i++){if(c[i].parentNode==u){this.l++}}
            if(this.v){;
                u.style.top=0; this.h=p.height||c[0].offsetHeight; u.style.height=(this.l*this.h)+'px'
            }else{
                u.style.left=0; this.w=p.width||c[0].offsetWidth; 
                u.style.width=(this.l*this.w)+'px'
            }
            this.nav(p.position||0);
            if(p.position){this.pos(p.position||0,this.a?1:0,1)}else if(this.a){this.auto()}
            if(p.left){this.sel(p.left)}
            if(p.right){this.sel(p.right)}
        },
        slide.prototype.auto=function(){
            this.x.ai=setInterval(new Function(this.n+'.move(1,1,1)'),this.a*1000)
        },
        slide.prototype.move=function(d,a){
            var n=this.c+d;
            if(this.r){n=d==1?n==this.l?0:n:n<0?this.l-1:n}
            this.pos(n,a,1)
        },
        slide.prototype.pos=function(p,a,m){
            var v=p; clearInterval(this.x.ai); clearInterval(this.x.si);
            if(!this.r){
                if(m){
                    if(p==-1||(p!=0&&Math.abs(p)%this.l==0)){
                        this.b++;
                        for(var i=0;i<this.l;i++){this.u.appendChild(this.m[i].cloneNode(1))}
                        this.v?this.u.style.height=(this.l*this.h*this.b)+'px':this.u.style.width=(this.l*this.w*this.b)+'px';
                    }
                    if(p==-1||(p<0&&Math.abs(p)%this.l==0)){
                        this.v?this.u.style.top=(this.l*this.h*-1)+'px':this.u.style.left=(this.l*this.w*-1)+'px'; v=this.l-1
                    }
                }else if(this.c>this.l&&this.b>1){
                    v=(this.l*(this.b-1))+p; p=v
                }
            }
            var t=this.v?v*this.h*-1:v*this.w*-1, d=p<this.c?-1:1; this.c=v; var n=this.c%this.l; this.nav(n);
            if(this.e){t=t-(8*d)}
            this.x.si=setInterval(new Function(this.n+'.slide('+t+','+d+',1,'+a+')'),10)
        },
        slide.prototype.nav=function(n){
            if(this.g){for(var i=0;i<this.l;i++){this.g[i].className=i==n?this.s:''}}
        },
        slide.prototype.slide=function(t,d,i,a){
            var o=this.v?parseInt(this.u.style.top):parseInt(this.u.style.left);
            if(o==t){
                clearInterval(this.x.si);
                if(this.e&&i<3){
                    this.x.si=setInterval(new Function(this.n+'.slide('+(i==1?t+(12*d):t+(4*d))+','+(i==1?(-1*d):(-1*d))+','+(i==1?2:3)+','+a+')'),10)
                }else{
                    if(a||(this.a&&this.p)){this.auto()}
                    if(this.b>1&&this.c%this.l==0){this.clear()}
                }
            }else{
                var v=o-Math.ceil(Math.abs(t-o)*.1)*d+'px';
                this.v?this.u.style.top=v:this.u.style.left=v
            }
        },
        slide.prototype.clear=function(){
            var c = this.u.getElementsByTagName('li');
            var t = i = c.length; 
            this.v ? this.u.style.top = 0 : this.u.style.left = 0;
            this.b=1; 
            this.c=0;
            for(i;i>0;i--){
                var e=c[i-1];
                if(t>this.l&&e.parentNode==this.u){this.u.removeChild(e); t--}
            }
        },
        slide.prototype.sel=function(i){
            var e = document.getElementById(i);
            e.onselectstart = e.onmousedown = function(){return false};
        }
        return{slide:slide}
    }();

    // inicia el plugin en caso de que este configurado para inicio automatico
    window.onload = function(){
        var autoInit = isAutoInit();
        if (autoInit && autoInit === 'true'){
            initWidget();
        }
    }

    return {

        init: initWidget,
        renderWidget: renderWidget,
        buttonStyleElement: buttonStyleElement,
        clickLogin: clickLogin
    }

}();