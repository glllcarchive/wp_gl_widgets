<?php

if(! class_exists("wp_gl_widgets"))
{
    class wp_gl_widgets
    {
        public static function select($list = array())
        {
            global $wpdb;

            $options = array();

            $items = array();
            $items['class']           = 'widget-select form-control';
            $items['add_class']       = '';
            $items['style']           = '';
            $items['sql']             = '';
            $items['name']            = '';
            $items['id']              = '';
            $items['value']           = '';
            $items['no_data_message'] = 'No Data';
            $items['select_one_flag'] = false;
            $items['select_one_text'] = 'Select One';
            $items['select_one_val']  = '';
            $items['disabled']        = false;
            $items['disabled_text']   = '';
            $items['attributes']      = array();
            $items['onchange']        = '';

            /**
            * Templates
            */
            $items['template']['select'] = '<select <!--ATTRIBUTES--> onchange="<!--ONCHANGE-->" class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->" style="<!--STYLE-->" <!--DISABLED-->><!--OPTIONS--></select>';
            $items['template']['option'] = '<option value="<!--VALUE-->" <!--SELECTED-->><!--TEXT--></option>';

            if(is_array($list) && ! empty($list))
            {
                $items = array_merge($items,$list);
            }

            /**
            * Ensure an id
            */
            if(empty($items['id']) && ! empty($items['name']))
            {
                $items['id'] = $items['name'];
            }

            if(! empty($items['disabled']))
            {
                $items['class'] .= ' disabled';
                $items['disabled_text'] = 'disabled';
            }

            if(! empty($items['add_class']))
            {
                $items['class'] .= " {$items['add_class']}";
            }

            if(! empty($items['width']))
            {
                $items['style'] .= "width:" . intval($items['width']) . "px";
            }

            if(! empty($items['sql']))
            {
                $rows = $wpdb->get_results($items['sql'], ARRAY_A);

                if(! $wpdb->last_error && ! empty($rows))
                {
                    if($items['select_one_flag'])
                    {
                        $pieces = array();
                        $pieces['<!--SELECTED-->'] = '';
                        $pieces['<!--VALUE-->']    = $items['select_one_val'];
                        $pieces['<!--TEXT-->']     = $items['select_one_text'];

                        $options[] = str_replace(array_keys($pieces), array_values($pieces), $items['template']['option']);
                    }

                    $is_option_grouped = false;

                    if(array_key_exists('group_option', $rows[0]))
                    {
                        $is_option_grouped = true;
                    }

                    /**
                    * Build out all of the options
                    */
                    foreach($rows as $row)
                    {
                        $pieces = array();
                        $pieces['<!--SELECTED-->'] = '';
                        $pieces['<!--VALUE-->']    = $row['value'];
                        $pieces['<!--TEXT-->']     = stripslashes($row['text']);

                        if(is_array($items['value']))
                        {
                            if(in_array($row['value'], $items['value']))
                            {
                                $pieces['<!--SELECTED-->'] = 'selected="selected"';
                            }
                        }
                        else
                        {
                            if($row['value'] == $items['value'])
                            {
                                $pieces['<!--SELECTED-->'] = 'selected="selected"';
                            }
                        }

                        if(! $is_option_grouped)
                        {
                            $options[] = str_replace(array_keys($pieces), array_values($pieces), $items['template']['option']);
                        }
                        else
                        {
                            $options[$row['group_option']][] = str_replace(array_keys($pieces), array_values($pieces), $items['template']['option']);
                        }
                    }
                }
            }

            /**
            * No data
            */
            if(empty($options))
            {
                $pieces = array();
                $pieces['<!--SELECTED-->'] = '';
                $pieces['<!--VALUE-->']    = '';
                $pieces['<!--TEXT-->']     = $items['no_data_message'];

                $options[] = str_replace(array_keys($pieces), array_values($pieces), $items['template']['option']);
            }
            elseif($is_option_grouped)
            {
                $options_tmp = array();

                foreach($options as $option_group => $merge_options)
                {
                    $options_tmp[] = "<optgroup label=\"$option_group\">" . implode('', $merge_options) . "</optgroup>";
                }

                $options = $options_tmp;
                unset($options_tmp);
            }

            $attributes = array();

            if(is_array($items['attributes']) && ! empty($items['attributes']))
            {
                foreach($items['attributes'] as $attr_key => $attr_value)
                {
                    $attributes[] = "$attr_key=\"$attr_value\"";
                }
            }

            $pieces = array();
            $pieces['<!--DISABLED-->']   = $items['disabled_text'];
            $pieces['<!--CLASS-->']      = $items['class'];
            $pieces['<!--NAME-->']       = $items['name'];
            $pieces['<!--ID-->']         = $items['id'];
            $pieces['<!--STYLE-->']      = $items['style'];
            $pieces['<!--OPTIONS-->']    = implode($options);
            $pieces['<!--ATTRIBUTES-->'] = implode(' ', $attributes);
            $pieces['<!--ONCHANGE-->']   = $items['onchange'];

            return str_replace(array_keys($pieces), array_values($pieces), $items['template']['select']);
        }

        public static function input($list = array())
        {
            global $wpdb;

            $items = array();
            $items['class']      = 'widget-input form-control';
            $items['add_class']  = '';
            $items['value']      = '';
            $items['type']       = 'text';
            $items['disabled']   = 'text';
            $items['passive']    = false;
            $items['name']       = '';
            $items['id']         = '';
            $items['style']      = '';
            $items['width']      = '';
            $items['attributes'] = array();

            /**
            * Templates
            */
            $items['template']['input'] = '<input <!--ATTRIBUTES--> style="<!--STYLE-->" type="<!--TYPE-->" class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->" value="<!--VALUE-->">';
            $items['template']['passive'] = '<div <!--ATTRIBUTES--> style="<!--STYLE-->" class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->"><!--VALUE--></div>';

            if(is_array($list) && ! empty($list))
            {
                $items = array_merge($items,$list);
            }

            if(! empty($items['width']))
            {
                $items['style'] .= "width:" . intval($items['width']) . "px";
            }

            $attributes = array();

            if(is_array($items['attributes']) && ! empty($items['attributes']))
            {
                foreach($items['attributes'] as $attr_key => $attr_value)
                {
                    $attributes[] = "$attr_key=\"$attr_value\"";
                }
            }

            if($items['passive'])
            {
                $items['template']['input'] = $items['template']['passive'];
                $items['add_class'] .= ' widget-input-passive';
            }

            if(! empty($items['add_class']))
            {
                $items['class'] .= " {$items['add_class']}";
            }

            $pieces = array();
            $pieces['<!--STYLE-->']      = $items['style'];
            $pieces['<!--CLASS-->']      = $items['class'];
            $pieces['<!--NAME-->']       = $items['name'];
            $pieces['<!--ID-->']         = $items['id'];
            $pieces['<!--VALUE-->']      = stripslashes($items['value']);
            $pieces['<!--TYPE-->']       = $items['type'];
            $pieces['<!--ATTRIBUTES-->'] = implode(' ', $attributes);

            return str_replace(array_keys($pieces), array_values($pieces), $items['template']['input']);
        }

        public static function button($list = array())
        {
            global $wpdb;

            $items = array();
            $items['class']      = 'btn';
            $items['add_class']  = '';
            $items['type']       = 'btn-primary';
            $items['text']       = 'Submit';
            $items['name']       = '';
            $items['id']         = '';
            $items['style']      = '';
            $items['width']      = '';
            $items['attributes'] = array();

            /**
            * Templates
            */
            $items['template']['input'] = '<div <!--ATTRIBUTES--> style="<!--STYLE-->" class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->"><!--TEXT--></div>';

            if(is_array($list) && ! empty($list))
            {
                $items = array_merge($items,$list);
            }

            if(! empty($items['width']))
            {
                $items['style'] .= "width:" . intval($items['width']) . ".px";
            }

            if(! empty($items['add_class']))
            {
                $items['class'] .= " {$items['add_class']}";
            }

            if(! empty($items['type']))
            {
                switch($items['type'])
                {
                    case 'btn-primary':
                    case 'btn-info':
                    case 'btn-success':
                    case 'btn-warning':
                    case 'btn-danger':
                    case 'btn-inverse':
                    case 'btn-link':

                    break;

                    default:
                        $items['type'] = '';
                }
            }

            if(! empty($items['type']))
            {
                $items['class'] .= " {$items['type']}";
            }

            $attributes = array();

            if(is_array($items['attributes']) && ! empty($items['attributes']))
            {
                foreach($items['attributes'] as $attr_key => $attr_value)
                {
                    $attributes[] = "$attr_key=\"$attr_value\"";
                }
            }

            $pieces = array();
            $pieces['<!--STYLE-->']   = $items['style'];
            $pieces['<!--CLASS-->']   = $items['class'];
            $pieces['<!--NAME-->']    = $items['name'];
            $pieces['<!--ID-->']      = $items['id'];
            $pieces['<!--TEXT-->']    = $items['text'];
            $pieces['<!--ATTRIBUTES-->'] = implode(' ', $attributes);

            return str_replace(array_keys($pieces), array_values($pieces), $items['template']['input']);
        }

        public static function textarea($list = array())
        {
            global $wpdb;

            $items = array();
            $items['class']      = 'widget-input';
            $items['value']      = '';
            $items['name']       = '';
            $items['id']         = '';
            $items['attributes'] = array();

            /**
            * Templates
            */
            $items['template']['input'] = '<textarea <!--ATTRIBUTES--> class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->"><!--VALUE--></textarea>';

            if(is_array($list) && ! empty($list))
            {
                $items = array_merge($items,$list);
            }

            $attributes = array();

            if(is_array($items['attributes']) && ! empty($items['attributes']))
            {
                foreach($items['attributes'] as $attr_key => $attr_value)
                {
                    $attributes[] = "$attr_key=\"$attr_value\"";
                }
            }

            $pieces = array();
            $pieces['<!--CLASS-->']      = $items['class'];
            $pieces['<!--NAME-->']       = $items['name'];
            $pieces['<!--ID-->']         = $items['id'];
            $pieces['<!--VALUE-->']      = stripslashes($items['value']);
            $pieces['<!--ATTRIBUTES-->'] = implode(' ', $attributes);

            return str_replace(array_keys($pieces), array_values($pieces), $items['template']['input']);
        }

        public static function radio()
        {
        }

        public static function check($list = array())
        {
            global $wpdb;

            $items = array();
            $items['class'] = 'widget-checkbox';
            $items['value'] = '';
            $items['name']  = '';
            $items['id']    = '';

            /**
            * Templates
            */
            $items['template']['input'] = '<input type="checkbox" class="<!--CLASS-->" name="<!--NAME-->" id="<!--ID-->" <!--CHECKED-->>';

            if(is_array($list) && ! empty($list))
            {
                $items = array_merge($items,$list);
            }

            $pieces = array();
            $pieces['<!--CHECKED-->'] = '';
            $pieces['<!--CLASS-->']   = $items['class'];
            $pieces['<!--NAME-->']    = $items['name'];
            $pieces['<!--ID-->']      = $items['id'];

            if(! empty($items['value']))
            {
                $pieces['<!--CHECKED-->'] = 'checked';
            }

            return str_replace(array_keys($pieces), array_values($pieces), $items['template']['input']);
        }

        public static function str_replace_field_widgets($field_name, $attributes, &$form_template)
        {
            if(is_array($attributes) && ! array_key_exists('name', $attributes))
            {
                $attributes['name'] = $field_name;
            }

            if(is_array($attributes) && ! array_key_exists('id', $attributes))
            {
                $attributes['id'] = $field_name;
            }

            $list = $attributes;

            if(is_array($attributes) && array_key_exists('attributes', $attributes))
            {
                $list = array_merge($list, (array) $attributes['attributes']);
            }

            if(is_array($attributes) && array_key_exists('type', $attributes))
            {
                switch($attributes['type'])
                {
                    case 'hidden':
                    case 'text':
                        $form_template = str_replace($attributes['tag'], wp_gl_besnard_widgets::input($list), $form_template);
                    break;
                    case 'textarea':
                        $form_template = str_replace($attributes['tag'], wp_gl_besnard_widgets::textarea($list), $form_template);
                    break;
                    case 'select':
                        $form_template = str_replace($attributes['tag'], wp_gl_besnard_widgets::select($list), $form_template);
                    break;
                    case 'check':
                        $form_template = str_replace($attributes['tag'], wp_gl_besnard_widgets::check($list), $form_template);
                    break;
                    case 'button':
                        $form_template = str_replace($attributes['tag'], wp_gl_besnard_widgets::button($list), $form_template);
                    break;
                }
            }

            return $form_template;
        }
    }
}
?>