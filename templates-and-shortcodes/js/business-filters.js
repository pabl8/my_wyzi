if(jQuery('#wyz-business-filter-keys').length && jQuery('.wyz_business_filter_field').length)
    initShortcodeJs();

function initShortcodeJs() {

    var checkList = document.getElementById('wyz-business-filter-keys');
    var savedData = jQuery('.wyz_business_filter_field').val();

    Vue.config.debug = true;
    Vue.config.devtools = true;

    var GridLayout = VueGridLayout.GridLayout;
    var GridItem = VueGridLayout.GridItem;


    var testLayout = [];

    function resize(index, newW) {
        var i=-1;
        for( i=0;i<testLayout.length;i++)
        {
            if(testLayout[i].i==index)break;
        }
        if(i==-1)return;

        if( newW<=8 && (newW%2 != 0) )
            testLayout[i].w=newW+1;
        else if(newW>8)
            testLayout[i].w = 12;
    }

    function fill_input() {
        var input = [];
        var o;
        for(var i=0;i<testLayout.length;i++)
        {
            o = '' == testLayout[i].o ? '' : JSON.stringify(testLayout[i].o);
            if(''!=o)o='('+trimExtrm(o)+')';
            input.push(testLayout[i].i+":("+testLayout[i].x+","+testLayout[i].y+","+testLayout[i].w+","+testLayout[i].h+","+o+")");
        }
        jQuery('.wpb_vc_param_value').val(input.join('::'));
    }

    function trimExtrm(str) {
        return str.substring(1,str.length-1);
    }

    var v = new Vue({
        el: '#app',
        components: {
            "GridLayout": GridLayout,
            "GridItem": GridItem
        },
        data: {
            layout: testLayout,
            index: 0,
            eventLog: []
        },
        watch: {
            eventLog: function() {}
        },
        methods: {
            moveEvent: function(i, newX, newY){},
            resizeEvent: function(i, newH, newW){
                fill_input();
            },
            movedEvent: function(i, newX, newY){
                fill_input();
            },
            resizedEvent: function(i, newH, newW, newHPx, newWPx){
                fill_input();
            },
            newResizeEvent: function(){
            },
        }
    });

    function getNameFromI(i){
        var val = '';
         jQuery('#wyz-business-filter-keys a').each(function(){
            if(jQuery(this).data('value')==i){
                val = jQuery(this).data('label');
                return val;
            }
        });
        return val;
    }

    savedData = savedData.trim();

    if(''!=savedData){
        savedData = savedData.split("::");
        var tmpCoor;
        var index;

        for(var i=0;i<savedData.length;i++){
            var tmpHolder=[];
            index = savedData[i].indexOf(":");
            tmpHolder.push(savedData[i].substr(0,index));
            tmpHolder.push(trimExtrm(savedData[i].substr(index+1)));
            var arr = tmpHolder[1].split(','),
            tmpCoor = arr.splice(0,4);
            tmpCoor.push(arr.join(','));
            tmpCoor[4] = '' == tmpCoor[4] ? '' : JSON.parse('{'+trimExtrm(tmpCoor[4])+'}');
            testLayout.push({"x":parseInt(tmpCoor[0]),"y":parseInt(tmpCoor[1]),"w":parseInt(tmpCoor[2]),"h":parseInt(tmpCoor[3]),"n":getNameFromI(tmpHolder[0]),"i":tmpHolder[0],"o":tmpCoor[4]});
            jQuery('#wyz-business-filter-keys #wyz-filter-'+tmpHolder[0]).parent().hide();
        }
    }


    Vue.config.debug = true;
    Vue.config.devtools = true;

    var GridLayout = VueGridLayout.GridLayout;
    var GridItem = VueGridLayout.GridItem;


    checkList.getElementsByClassName('anchor')[0].onclick = function (evt) {
        if (checkList.classList.contains('visible')){
            checkList.classList.remove('visible');
        }
        else
            checkList.classList.add('visible');
    }

    jQuery('#wyz-business-filter-keys li>a').on('click', function(e){
        e.preventDefault();
        var val = jQuery(this).data('value');
        var name = jQuery(this).data('label');
        testLayout.push({"x":0,"y":0,"w":2,"h":2,"n":name,"i":val,"o":""});
        jQuery(this).parent().hide();
        fill_input();
    });


    jQuery('.vue-grid-item .remove-filter').live('click',function(){
        var id =jQuery(this).siblings('.filter-id').html();
        var i;
        for(i=0;i<testLayout.length;i++)
        {
            if(testLayout[i].i == id)
                break;
        }
        if (i<testLayout.length) {
            testLayout.splice(i, 1);
            jQuery('#wyz-business-filter-keys #wyz-filter-'+id).parent().show();
            fill_input();
        }
    });



    jQuery(document).on('wyz-filter-option-change',function(e,id,key,value){
        var i;
        for(i=0;i<testLayout.length;i++)
        {
            if(testLayout[i].i == id){
                if(testLayout[i].o === null || typeof testLayout[i].o !== 'object')
                    testLayout[i].o = {};
                testLayout[i].o[key] = value;
                fill_input();
                break;
            }
        }
    });



    jQuery('.vue-grid-item').live('click',function(){
        var id =jQuery(this).find('.filter-id').html();
        jQuery('.wyz-filter-options-cont').html(businessFilters.optionsHtml[id]);
        for(var i=0;i<testLayout.length;i++)
        {
            if(testLayout[i].i == id){
                if(testLayout[i].o !== null && typeof testLayout[i].o == 'object') {
                    var tmlHolder;
                    for(var tmp in testLayout[i].o){
                        if(jQuery('.filter-options [data-key="'+tmp+'"]').length){;
                            jQuery('.filter-options [data-key="'+tmp+'"]').val(testLayout[i].o[tmp]);
                        }
                    }
                }
                break;
            }
        }
    });
}

