( function( wp ) {
    const { registerBlockType } = wp.blocks;
    const { TextControl, CheckboxControl } = wp.components;
    const { __ } = wp.i18n;
    const { Fragment } = wp.element;

    registerBlockType( 'rescue-sync/adoptable-pets', {
        title: __( 'Adoptable Pets', 'rescuegroups-sync' ),
        icon: 'pets',
        category: 'widgets',
        attributes: {
            number: {
                type: 'number',
                default: 5
            },
            featured_only: {
                type: 'boolean',
                default: false
            }
        },
        edit: function( props ) {
            const { attributes, setAttributes } = props;
            return wp.element.createElement(
                Fragment,
                null,
                wp.element.createElement( 'p', null, __( 'This block displays a list of adoptable pets.', 'rescuegroups-sync' ) ),
                wp.element.createElement( TextControl, {
                    label: __( 'Number of pets', 'rescuegroups-sync' ),
                    type: 'number',
                    value: attributes.number,
                    onChange: function( value ) { setAttributes( { number: parseInt( value, 10 ) || 0 } ); }
                } ),
                wp.element.createElement( CheckboxControl, {
                    label: __( 'Only show featured', 'rescuegroups-sync' ),
                    checked: attributes.featured_only,
                    onChange: function( value ) { setAttributes( { featured_only: !! value } ); }
                } )
            );
        },
        save: function() {
            return null;
        }
    } );
} )( window.wp );
