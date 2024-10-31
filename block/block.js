const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor || wp.editor;
const { PanelBody, TextControl, SelectControl, ColorPicker } = wp.components;

registerBlockType( 'pswc/product-subtitle-block', {
    title: __( 'Product Subtitle', 'product-subtitle-for-woocommerce' ),
    icon: 'archive',
    category: 'widgets',
    attributes: {
        htmlTag: { type: 'string', default: 'div' },
        fallbackText: { type: 'string', default: 'Default Subtitle' },
        textColor: { type: 'string', default: 'inherit' },
        backgroundColor: { type: 'string', default: 'transparent' },
    },
    edit: ( props ) => {
        const { attributes, setAttributes } = props;
        const { htmlTag, fallbackText, textColor, backgroundColor } = attributes;
        const TagName = htmlTag || 'div'; // Declare TagName here
    
        return (
            <>
                <InspectorControls>
                    <PanelBody title={ __( 'Settings', 'product-subtitle-for-woocommerce' ) }>
                        <SelectControl
                            label={ __( 'HTML Tag', 'product-subtitle-for-woocommerce' ) }
                            value={ htmlTag }
                            options={[
                                { label: '<div>', value: 'div' },
                                { label: '<p>', value: 'p' },
                                { label: '<span>', value: 'span' },
                                { label: '<h1>', value: 'h1' },
                                { label: '<h2>', value: 'h2' },
                                { label: '<h3>', value: 'h3' },
                                { label: '<h4>', value: 'h4' },
                                { label: '<h5>', value: 'h5' },
                                { label: '<h6>', value: 'h6' },
                            ]}
                            onChange={ ( htmlTag ) => setAttributes( { htmlTag } ) }
                        />
                        <TextControl
                            label={ __( 'Fallback Subtitle Text', 'product-subtitle-for-woocommerce' ) }
                            value={ fallbackText }
                            onChange={ ( fallbackText ) => setAttributes( { fallbackText } ) }
                        />
                        <label>{ __( 'Text Color', 'product-subtitle-for-woocommerce' ) }</label>
                        <ColorPicker
                            color={ textColor }
                            onChangeComplete={ ( value ) => setAttributes( { textColor: value.hex } ) }
                        />
                        <label>{ __( 'Background Color', 'product-subtitle-for-woocommerce' ) }</label>
                        <ColorPicker
                            color={ backgroundColor }
                            onChangeComplete={ ( value ) => setAttributes( { backgroundColor: value.hex } ) }
                        />
                    </PanelBody>
                </InspectorControls>
                <div className="product-subtitle">
                    <TagName style={{ color: textColor, backgroundColor }}>{ fallbackText }</TagName>
                </div>
            </>
        );
    },
    save() {
        return null; // Rendered in PHP
    },
} );
