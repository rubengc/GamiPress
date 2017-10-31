<?php
/**
 * Default Email Template: Header
 *
 * This template can be overridden by copying it to yourtheme/gamipress/emails/header-default.php
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.

$body = "
	background-color: #f6f6f6;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
";

$wrapper = "
	width:100%;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 70px 0 0 0;
";

$template_container = "
	box-shadow:0 0 0 1px #f3f3f3 !important;
	background-color: #ffffff;
	border: 1px solid #e9e9e9;
	padding: 20px;
";

$template_header = "
	color: #00000;
	border-top-left-radius:3px !important;
	border-top-right-radius:3px !important;
	border-bottom: 0;
	font-weight:bold;
	line-height:100%;
	text-align: center;
	vertical-align:middle;
";

$body_content = "
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
";

$body_content_inner = "
	color: #000000;
	font-size:14px;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
	text-align:left;
";

$header_img = gamipress_get_option( 'email_logo', '' );
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name' ); ?></title>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="<?php echo $body; ?>">
		<div style="<?php echo $wrapper; ?>" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
						<?php if( ! empty( $header_img ) ) : ?>
							<div id="template_header_image">
								<?php echo '<p style="margin-top:0;"><img src="' . esc_url( $header_img ) . '" alt="' . get_bloginfo( 'name' ) . '" /></p>'; ?>
							</div>
						<?php endif; ?>
						<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="<?php echo $template_container; ?>">
							<tr>
								<td align="center" valign="top">
									<!-- Body -->
									<table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
										<tr>
											<td valign="top" style="<?php echo $body_content; ?>">
												<!-- Content -->
												<table border="0" cellpadding="20" cellspacing="0" width="100%">
													<tr>
														<td valign="top">
															<div style="<?php echo $body_content_inner; ?>">