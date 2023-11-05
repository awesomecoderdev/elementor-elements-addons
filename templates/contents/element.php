<#
    view.addRenderAttribute('wrapper' ,{
        'id' : 'custom-widget-id' ,
        'class' : [ 'custom-widget-wrapper-class eea-widget-heading' , settings.custom_class ],
        'role' : settings.role,
        'aria-label' : settings.name,
    });

    view.addInlineEditingAttributes( 'text' , 'none' );
#>
    <h1 {{{ view.getRenderAttributeString( 'title' ) }}}>{{{settings.title}}}</h1>