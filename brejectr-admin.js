jQuery(document).ready( function() {
	// the script for displaying each options subpage without needing to reload the settings page
	jQuery('#brejectr_tor').hide();
	jQuery('#brejectr_preview').hide();
	jQuery('#brejectrsuboptionspage_win').addClass( 'brejectrcurrent' );
	jQuery('#brejectrsuboptionspage_tor').click( function(){ 
		jQuery('#brejectr_win').hide(300); 
		jQuery('#brejectr_preview').hide(300);
		jQuery('#brejectr_tor').show(300); 
		jQuery('#brejectrsubpages .brejectrcurrent').removeClass( 'brejectrcurrent' );
		jQuery(this).addClass( 'brejectrcurrent' );
	} );
	jQuery('#brejectrsuboptionspage_win').click( function(){ 
		jQuery('#brejectr_tor').hide(300); 
		jQuery('#brejectr_preview').hide(300);
		jQuery('#brejectr_win').show(300); 
		jQuery('#brejectrsubpages .brejectrcurrent').removeClass( 'brejectrcurrent' );
		jQuery(this).addClass( 'brejectrcurrent' );
	} );
	jQuery('#brejectrsuboptionspage_preview').click( function(){ 
		jQuery('#brejectr_tor').hide(300);
		jQuery('#brejectr_win').hide(300);
		jQuery('#brejectr_preview').show(300);
		jQuery('#brejectrsubpages .brejectrcurrent').removeClass( 'brejectrcurrent' );
		jQuery(this).addClass( 'brejectrcurrent' );
	} );
	
// hide the close link options when closing is disabled
	jQuery('#closeno').click(function(){ jQuery('#closemessage').hide(500); jQuery('#closelink').hide(500); jQuery('#closecookies').hide(500); jQuery('#closeie67').hide(500); });
	jQuery('#closeyes').click(function(){ jQuery('#closemessage').show(500); jQuery('#closelink').show(500); jQuery('#closecookies').show(500); jQuery('#closeie67').show(500); });
	
});