# CMB2 RGBa Colorpicker

A RGBa colorpicker for [CMB2](https://github.com/WebDevStudios/CMB2), I couldn't find one, so I made this plugin, that is all.

Big thanks to [23r9io](https://github.com/23r9i0/wp-color-picker-alpha) for the JS.

## Usage
```
array(
	'name'    => __( 'RGBa Colorpicker', 'cmb2' ),
	'desc'    => __( 'Field description (optional)', 'cmb2' ),
    'id'   => $prefix . 'test_colorpicker',
    'type' => 'rgba_colorpicker',
    'default'  => '#ffffff',
),
```

## Changelog

### 0.2.0
* Fixes [#2](https://github.com/JayWood/CMB2_RGBa_Picker/issues/2) - Repeatable groups fix thanks to [leolweb](https://github.com/leolweb)

### 0.1.0
* Initial Commit