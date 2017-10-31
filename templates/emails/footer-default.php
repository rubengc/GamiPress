<?php
/**
 * Default Email Template: Footer
 *
 * This template can be overridden by copying it to yourtheme/gamipress/emails/footer-default.php
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
$template_footer = "
	border-top:0;
";

$credit = "
	border:0;
	color: #000000;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
	font-size:12px;
	line-height:125%;
	text-align:center;
";
?>
															</div>
														</td>
                                                    </tr>
                                                </table>
                                                <!-- End Content -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Body -->
                                </td>
                            </tr>
                        </table>
                        <!-- Footer -->
                        <table border="0" cellpadding="10" cellspacing="0" width="520" id="template_footer" style="<?php echo $template_footer; ?>">
                            <tr>
                                <td valign="top">
                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                        <tr>
                                            <td colspan="2" valign="middle" id="credit" style="<?php echo $credit; ?>">
                                                <?php echo wpautop( wp_kses_post( wptexturize(
                                                    apply_filters( 'gamipress_email_footer_text', gamipress_get_option( 'email_footer_text', sprintf( __( '%s - Powered by GamiPress', 'gamipress' ), '<a href="' . esc_url( home_url() ) . '">' . get_bloginfo( 'name' ) . '</a>' ) ) )
                                                ) ) ); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <!-- End Footer -->
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>