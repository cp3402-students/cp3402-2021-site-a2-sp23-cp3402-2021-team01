( function( blocks, editor,  element ) {
    var el = wp.element.createElement;
    var InspectorControls = editor.InspectorControls;
    var PanelRow = wp.components.PanelRow;
    var PanelBody = wp.components.PanelBody;

    var iconEl = el('svg', {width:20, height:20,  viewBox: '0 0 1080 1080', class:'ifso-block-icon' },[ el('path', { d: "M418.9,499.8c-32.2,0-61.5,0-92.2,0c0-46.7,0-92.6,0-140c29.8,0,59.6,0,91.9,0c0-7.6-0.7-14,0.1-20.1c4.6-32.2,5.5-65.6,15.3-96.2c19.4-60.5,67.6-90.1,127.1-102.1c67.4-13.6,135.3-6.5,204.2-3c0,51.9,0,102.8,0,155.4c-15.7-1.8-30.7-3.7-45.6-5.2c-7.5-0.8-15.2-1.7-22.7-1.2c-43.8,3.2-61,25.8-53.6,71.6c38.1,0,76.5,0,116.2,0c0,47,0,92.5,0,139.9c-37.1,0-74.3,0-113.2,0c0,152.1,0,302.3,0,453.7c-76.3,0-151,0-227.5,0C418.9,802.1,418.9,652,418.9,499.8z", class:'st0'})
        ,el('path', { d: "M0,134.5c83.7,0,166.3,0,250,0c0,272.8,0,544.9,0,818.3c-82.8,0-165.8,0-250,0C0,680.8,0,408.3,0,134.5z", class:'st0'}),
        el('path', {style: {fill:'#FD5B56'},  d: "M893.5,392.3c62.2,44.4,123.4,88.1,185.8,132.7c-62.2,44.4-123.3,88-185.8,132.7C893.5,568.8,893.5,481.5,893.5,392.3z", class:'st1'})]);

    var data_rules_model = (typeof(data_rules_model_json)==='string') ? JSON.parse(data_rules_model_json) : data_rules_model_json;
    var license_status_object = (typeof(license_status)==='string') ? JSON.parse(license_status) : license_status;
    var pages_links = (typeof(ifso_pages_links)==='string') ? JSON.parse(ifso_pages_links) : ifso_pages_links;

    console.log(data_rules_model);

    function get_form_data(form){
        var arr = jQuery(form).serializeArray();
        var ret ={};
        arr.forEach(function(field){
            ret[field.name] = field.value;
        });
        return ret;
    }

    function save_form_data(form,props){
        var interacted_form_data = get_form_data(form);
        props.setAttributes({ifso_condition_rules:interacted_form_data});
    }

    function create_sidebar_condition_ui_elements(props){
        //var title = el('h3',{className:'title'},iconEl,'Dynamic Content');
        var title = null;

        var trigger_type_select = el('select',{onChange:function(e){
                    var rules_form_wrap = e.target.parentElement.parentElement.querySelector('.ifso-standalone-condition-form-wrap');
                    var selected_value = e.target.selectedOptions[0].value;
                    var old_selected_form = rules_form_wrap.querySelector('.selected');
                    if (old_selected_form){
                        old_selected_form.classList.remove('selected');
                        hard_reset_form(old_selected_form);
                    }
                    props.setAttributes({ifso_condition_type : selected_value, ifso_condition_rules : {}});
                    var new_selected_form = rules_form_wrap.querySelector('[formtype="'+selected_value+'"]');
                    if (selected_value){
                        new_selected_form.classList.add('selected');
                        save_form_data(new_selected_form,props);
                        var subgroup = new_selected_form.getAttribute('contains_subgroups');
                        if(null!==subgroup && subgroup){
                            //switch_form_to_subgroup(new_selected_form,subgroup);
                        }
                    }}},
            function(){
                var ret = [];
                var noneSelected = (''===props.attributes.ifso_condition_type);
                ret.push(el('option',{value:'',selected:noneSelected},null,'Select a Condition'));
                Object.keys(data_rules_model).forEach(function(type){if(type==='general' || type==='AB-Testing') return;var selected = (type===props.attributes.ifso_condition_type);var not_allowed_marker = (!license_status_object['is_license_valid'] && !in_array(license_status_object['free_conditions'],type)) ? '*' : '';ret.push(el('option',{value:type, selected:selected},data_rules_model[type]['name']+not_allowed_marker))});
                return ret;
            }());
        var trigger_type_wrap = el('div',{className:'ifso-standalone-condition-trigger-type-wrap'},[el('label',{},null,'Only show this block if: '),trigger_type_select]);

        var trigger_rules_form = el('div',{className:'ifso-standalone-condition-form-wrap'},create_data_rules_forms(data_rules_model,props));

        var default_content_wrap = el('div',{className:'default-content-wrap'},[el('input',{type:'checkbox',className:'default-content-exists-input input-control',checked:props.attributes.ifso_default_exists,onChange:function(e){props.setAttributes({ifso_default_exists: e.target.checked})}}),
            el('label',{className:(props.attributes.ifso_default_exists) ? '' : 'ifso-gray'},null,'Default Content:'), el(wp.blockEditor.RichText,{value: props.attributes.ifso_default_content, className:((props.attributes.ifso_default_exists) ? '' : 'nodisplay ') + 'default-content-input block-editor-plain-text input-control',placeholder:'Type here (HTML)',onChange:function(e){props.setAttributes({ifso_default_content : e});}})
        ])

        var aud_add_rm_wrap = el('div',{className:'audiences-addrm-wrap'},[el('input',{type:'checkbox',className:'audiences-addrm-exists-input',checked:!(is_empty(props.attributes.ifso_aud_addrm)),onChange:function(e){var toSet = (e.target.checked) ? {add:[],rm:[]} : {}; props.setAttributes({ifso_aud_addrm : toSet})} }),
        el('label',{className:(is_empty(props.attributes.ifso_aud_addrm)) ? 'ifso-gray' : ''},null,'Audiences'),create_audience_addrm_ui(props)]);

        var base_div = el('div',{className:'custom-condition-base-div'},[title,trigger_type_wrap,trigger_rules_form,default_content_wrap,aud_add_rm_wrap]);

        return base_div;
    }

    function create_data_rules_forms(model,props){
        var ret = [];
        if(model && typeof(model)==='object'){
            Object.keys(model).forEach(function(condition){
                var form = create_data_rules_form(model,condition,props);
                ret.push(form);
            });
        }
        return ret;
    }

    function create_data_rules_form(model,condition,props){
        if(model && typeof(model)==='object' && condition){
            var form_elements = [];
            form_elements.push(create_license_condition_message(condition));
            var selected_form = (condition===props.attributes.ifso_condition_type);
            var contains_subgroups = false;
            var switcher_value = null;
            if(model[condition]){
                Object.keys(model[condition]['fields']).forEach(function(index){
                    var created_element = createElementFromModel(model[condition]['fields'][index],props,selected_form);
                    if (created_element.props.subgroup) contains_subgroups = true;
                    if (created_element.props.is_switcher) switcher_value = created_element.props.switcher_init_value;
                    form_elements.push(created_element);
                });
            }
            if(condition === 'Geolocation') form_elements.push(el('a',{href:'https://www.if-so.com/country-codes-and-states-names-iso-3166/',target:'_blank'},'How to set up this condition')); //Temporary!
            var form = el('form',{className:'ifso-standalone-condition-form',formType:condition,onSubmit:function(e){e.preventDefault();}},form_elements);
            if(selected_form) form.props.className += ' selected';
            if(contains_subgroups) form.props.contains_subgroups = 'true';
            if(contains_subgroups) switch_form_to_subgroup_two(form,switcher_value);
            return form;
        }
    }



    function createElementFromModel(elObj,props,fillWitData=false){
        if(elObj && typeof(elObj)==='object'){
            var ret;
            var element;
            var label = null;
            var saveInteractedFormData = function(e){
                var interacted_form = jQuery(e.target).closest('form');

                if(elObj['is_switcher']){
                    var switched_to = e.target.value;
                    switch_form_to_subgroup(interacted_form[0],switched_to);
                }

                var symbol = e.target.getAttribute('symbol');   //Multibox stuff
                var geo_symbols =['CITY','COUNTRY','STATE','CONTINENT'];
                if(symbol){
                    var data_container = interacted_form.find('[multiData]');

                    if(geo_symbols.includes(symbol)){
                        var changed_input_val = jQuery(e.target).val();
                        data_container.val(createNewLocation(symbol,changed_input_val,changed_input_val))
                    }

                    else{
                        var symbol_inputs = interacted_form.find('[symbol='+symbol+']');
                        console.log(symbol_inputs);
                        data_container.val(createNewLocation(symbol,jQuery(symbol_inputs[0]).val(),jQuery(symbol_inputs[1]).val()));
                    }

                }

                save_form_data(interacted_form,props);
            };

            if(elObj['type'] === 'noticebox'){
                return create_noticebox(elObj);
            }

            if(elObj['type']==='text'){
                element = el('input',{type:'text',placeholder:elObj['prettyName'],name:elObj['name'],required:elObj['required'],onChange:saveInteractedFormData})
            }

            if(elObj['type']==='select'){
                var select_options = create_ifso_ui_select_options(elObj['options']);
                element = el('select',{name:elObj['name'],required:elObj['required'],onChange:saveInteractedFormData},select_options);
            }

            if(elObj['type']==='checkbox'){
                element= el('input',{type:'checkbox',name:elObj['name'],onChange:saveInteractedFormData});
                label = el('label',{for:elObj['name']},null,elObj['prettyName']);
            }

            if(elObj['type']==='multi'){
                element = el('input',{type:'text', name:elObj['name'], hidden:true, multiData:'true', onChange:saveInteractedFormData});
            }

            element.props.className = elObj['extraClasses'];

            if(null !== elObj['symbol'] ){
                element.props.symbol = elObj['symbol'];
            }

            if(element.props.className === 'countries-autocomplete'){

            }

            ret = el('div',{className:'ifso-standalone-condition-control-wrap'},null,[element,label])

            if(fillWitData && props.attributes.ifso_condition_rules[elObj.name]){

                element.props.value = props.attributes.ifso_condition_rules[elObj.name];

                if(elObj['type']==='checkbox' && props.attributes.ifso_condition_rules[elObj.name]){
                    if( 'on' === props.attributes.ifso_condition_rules[elObj.name])
                        element.props.checked = true;
                    else
                        element.props.checked = false;

                }

            }

            if(null!==elObj['subgroup']){
                ret.props.subgroup = elObj['subgroup'];
            }

            if(elObj['is_switcher']){
                ret.props.is_switcher = true;
                ret.props.className += ' is_switcher';
                ret.props.switcher_init_value = element.props.value;
            }

            return ret;
        }
    }

    function switch_form_to_subgroup(form,subgroup){
        jQuery(form).attr('showing_subgroup',subgroup);
        form.querySelectorAll('[subgroup]').forEach(function(e){
            if(subgroup === e.getAttribute('subgroup')){
                e.classList.remove('nodisplay');
            }
            else{
                e.classList.add('nodisplay');
            }
        });
    }

    function switch_form_to_subgroup_two(form,subgroup){
        form.props.showing_subgroup = subgroup;
        form.props.children.forEach(function(el){
            if(null===el || el.props.className!=='ifso-standalone-condition-control-wrap') return;
            if(subgroup === el.props.subgroup){
                el.props.className = el.props.className.replace(' nodisplay','');
            }
            else if(el.props.subgroup){
                el.props.className += ' nodisplay'
            }
        });
    }

    function create_ifso_ui_select_options(optionsArr){
        var ret = [];
        if(optionsArr && optionsArr.length>0){
            optionsArr.forEach(function(opt){
                ret.push(el('option',{value:opt['value']},null,opt['display_value']));
            })
        }
        return ret;
    }

    function create_license_condition_message(condition){
        var ret = null;
        var get_license_url = 'https://www.if-so.com/plans/?utm_source=Plugin&utm_medium=direct&utm_campaign=getFree&utm_term=lockedConditon&utm_content=Gutenberg';
        if(!license_status_object['is_license_valid'] && !in_array(license_status_object['free_conditions'],condition)){
            ret = el('div',{error_message:'1',className:'ifso-stantalone-error-message'},null,[
                el('a',{href:get_license_url, target:'_blank'},'This condition is only available upon license activation. Click here to get a free license if you do not have one.')
            ]);
        }
        return ret;
    }

    function hard_reset_form(form){
        form.reset();
        jQuery(form).find(':input').each(function() {
            switch(this.type){
                case 'textarea':
                case 'text':
                    jQuery(this).val('');
            }
        });
    }

    function in_array(array,member){
        if(array.indexOf(member)===-1){
            return false;
        }
        return true;
    }

    function is_empty(obj) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop)) {
                return false;
            }
        }

        return JSON.stringify(obj) === JSON.stringify({});
    }

    function create_noticebox(elObj){
        var ret = el('div',{className:'ifso-standalone-condition-noticebox'},null,elObj['content']);

        if(null!==elObj['subgroup']){
            ret.props.subgroup = elObj['subgroup'];
        }

        if(elObj['closeable']==true){
            ret.props.children.push(el('span',{className:'closingX',onClick:function(e){e.target.parentElement.classList.add('nodisplay');}},'X'));
        }

        ret.props.style = {color:elObj['color'],backgroundColor:elObj['bgcolor'],border:'1px solid' +elObj['color']};

        return ret;
    }

    function create_audience_addrm_ui(props){
        if(data_rules_model['Groups']['fields']['group-name']['options'] ){
            var groupsList = data_rules_model['Groups']['fields']['group-name']['options'];

            var updateStatus = function(e){
                var statusType = (e.target.name === 'ifso-aud-add') ? 'add' : 'rm' ;
                var otherStatusType = (statusType==='add') ? 'rm' : 'add';
                var statusUpdate = jQuery(e.target.parentElement).find('input').serializeArray().map(function(val){return val['value']});

                var full_status = {};
                full_status[statusType] = statusUpdate;
                full_status[otherStatusType] = props.attributes.ifso_aud_addrm[otherStatusType] || [];

                props.setAttributes({ifso_aud_addrm:full_status});

            };

            var create_addrm_form = function(type='add'){
                var checkSelects = groupsList.map( function(val){return [el('input',{type:'checkbox',checked : (props.attributes.ifso_aud_addrm && props.attributes.ifso_aud_addrm !== null && !is_empty(props.attributes.ifso_aud_addrm) && Object.prototype.toString.call(props.attributes.ifso_aud_addrm[type])==='[object Array]' && in_array(props.attributes.ifso_aud_addrm[type],val['value'])), name:'ifso-aud-'+type,value:val['value'],onChange:updateStatus}),el('label',{},null,val['display_value']),el('br')] });
                var form = el('form',{className:'ifso-aud-addrm-form'},checkSelects);
                return form;
            };

            var aud_addrm_ui = el('div',{className:'ifso-aud-addrm-ui-wrap '+((is_empty(props.attributes.ifso_aud_addrm)) ? 'nodisplay' : '')}, (groupsList && groupsList.length > 0) ?
                [el('p',{},null,['Add or remove users from the following audiences when the version is displayed. ',el('a',{href:'https://www.if-so.com/help/documentation/segments/?utm_source=Plugin&utm_medium=Micro&utm_campaign=GutenbergGroups', target:'_blank'},'Learn More')]),
                    el('h4',{},null,'Add to audiences:'),create_addrm_form('add'), el('h4',{},null,'Remove from audiences:'),create_addrm_form('rm')]
                :
                el('p', {className: 'ifso-no-aud-error'}, 'You haven\'t created any audiences yet. ', el('a', {href: pages_links['gropus_page'], target: '_blank'}, 'Create an audience'), el('span', {},' (and refresh).')));


            return aud_addrm_ui;
        }


    }




    wp.hooks.addFilter( 'blocks.registerBlockType', 'ifso/ifso-standalone-conditions-block-filter', function(opts,name){

        opts.attributes = {
            ...opts.attributes,
            ifso_condition_type:{
                type:'string',
                default:''
            },
            ifso_condition_rules:{
                type:'object',
                default:{}
            },
            ifso_default_exists:{
                type:'boolean',
                default:false
            },
            ifso_default_content:{
                type:'string',
                default:''
            },
            ifso_aud_addrm: {
                type:'object',
                default: {}
            }
        }


        return opts;
    } );


    var withIfSoSidebar = wp.compose.createHigherOrderComponent( function( BlockEdit ) {
        return function( props ) {
            var isOpen = (props.attributes.ifso_condition_type !== '');
            return el(
                wp.element.Fragment,
                {},
                el('div', {className: 'ifso-block-wrap wp-block', style: {position: 'relative'}},
                    el(
                        BlockEdit,
                        props
                    ),
                    (isOpen) ? el('span', {className: 'ifso-has-standalone-marker'}, 'If',el('span',{style:{color:'#fd5b56'}},'\u2023'),'So active') : null
                ),
                el(wp.blockEditor.InspectorControls,
                    {},
                    el(
                        PanelBody,
                        {className:'ifso-condition-sidebar-wrap',initialOpen:isOpen,title:el('span',{className:'title'},iconEl,'Dynamic Content')},
                        el(PanelRow,{},create_sidebar_condition_ui_elements(props)),
                        ''
                    )
                )
            );
        };
    }, 'withIfSoSidebar' );

    wp.hooks.addFilter( 'editor.BlockEdit', 'ifso/ifso-standalone-conditions-block-filter-edit',withIfSoSidebar);

} )( window.wp.blocks, window.wp.editor, window.wp.element );
