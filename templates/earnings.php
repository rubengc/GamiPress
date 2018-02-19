<?php
/**
 * User Earnings template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/earnings.php
 */
global $gamipress_template_args;

// Shorthand
$a = $gamipress_template_args;

// Setup vars
$requirement_types = gamipress_get_requirement_types();
$points_types = gamipress_get_points_types();
$achievement_types = gamipress_get_achievement_types();
$rank_types = gamipress_get_rank_types();

// Execute the query
$user_earnings = $a['query']->get_results();

?>

<div class="gamipress-earnings">

    <?php
    /**
     * Before render earnings
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_before_render_earnings', $a ); ?>

    <?php if( $a['query']->found_results > 0 ) : ?>

        <?php
        $earnings_columns = array(
            'thumbnail'     => __( 'Thumbnail', 'gamipress' ),
            'description'   => __( 'Description', 'gamipress' ),
            'date'          => __( 'Date', 'gamipress' ),
            'points'        => __( 'Points', 'gamipress' ),
        );

        /**
         * Earnings columns
         *
         * @since 1.0.0
         *
         * @param array $earnings_columns   Earnings columns to be rendered
         * @param array $template_args      Template received arguments
         */
        $earnings_columns = apply_filters( 'gamipress_earnings_columns', $earnings_columns, $a );
        ?>

        <table id="gamipress-earnings-table" class="gamipress-earnings-table">

            <thead>

                <tr>

                    <?php foreach( $earnings_columns as $column_name => $column_label ) : ?>
                        <th class="gamipress-earnings-col gamipress-earnings-col-<?php echo $column_name; ?>"><?php echo $column_label; ?></th>
                    <?php endforeach ?>

                </tr>

            </thead>

            <tbody>

            <?php foreach( $user_earnings as $user_earning ) : ?>

                <tr>

                    <?php foreach( $earnings_columns as $column_name => $column_label ) : ?>

                        <?php
                        $column_output = '';

                        switch( $column_name ) {
                            case 'thumbnail':

                                if( in_array( $user_earning->post_type, gamipress_get_requirement_types_slugs() ) ) {

                                    if( $user_earning->post_type === 'step' && $achievement = gamipress_get_parent_of_achievement( $user_earning->post_id ) )  {
                                        // Step

                                        // Get the achievement thumbnail and build a link to the achievement
                                        $column_output = gamipress_get_achievement_post_thumbnail( $achievement->ID );

                                    } else if( ( $user_earning->post_type === 'points-award' || $user_earning->post_type === 'points-deduct' ) && $points_type = gamipress_get_points_award_points_type( $user_earning->post_id ) )  {
                                        // Points Award and Deduct

                                        // Get the points type thumbnail
                                        $column_output = gamipress_get_points_type_thumbnail( $points_type->ID );

                                    } else if( $user_earning->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $user_earning->post_id ) ) {
                                        // Rank requirement

                                        // Get the rank thumbnail
                                        $column_output = gamipress_get_rank_post_thumbnail( $rank->ID );
                                    }

                                } else if( in_array( $user_earning->post_type, gamipress_get_achievement_types_slugs() ) ) {
                                    // Achievement

                                    // Get the achievement thumbnail
                                    $column_output = gamipress_get_achievement_post_thumbnail( $user_earning->post_id );

                                } else if( in_array( $user_earning->post_type, gamipress_get_rank_types_slugs() ) ) {
                                    // Rank

                                    // Get the rank thumbnail
                                    $column_output = gamipress_get_rank_post_thumbnail( $user_earning->post_id );

                                }

                                break;
                            case 'description':

                                if( in_array( $user_earning->post_type, gamipress_get_requirement_types_slugs() ) ) {

                                    $earning_title = gamipress_get_post_field( 'post_title', $user_earning->post_id );
                                    $earning_description = '';

                                    if( $user_earning->post_type === 'step' && $achievement = gamipress_get_parent_of_achievement( $user_earning->post_id ) )  {
                                        // Step

                                        // Build a link to the achievement
                                        $earning_description = sprintf( '%s %s: <a href="%s">%s</a>',
                                            $achievement_types[$achievement->post_type]['singular_name'],
                                            __( 'Step', 'gamipress' ),
                                            get_post_permalink( $achievement->ID ),
                                            gamipress_get_post_field( 'post_title', $achievement->ID )
                                        );

                                    } else if( ( $user_earning->post_type === 'points-award' || $user_earning->post_type === 'points-deduct' ) && $points_type = gamipress_get_points_award_points_type( $user_earning->post_id ) )  {
                                        // Points Award and Deduct

                                        $earning_description = sprintf( '%s %s',
                                            $points_types[$points_type->post_name]['plural_name'],
                                            ( $user_earning->post_type === 'points-award' ? __( 'Award', 'gamipress' ) : __( 'Deduction', 'gamipress' ) )
                                        );

                                    } else if( $user_earning->post_type === 'rank-requirement' && $rank = gamipress_get_rank_requirement_rank( $user_earning->post_id ) ) {
                                        // Rank requirement

                                        // Build a link to the rank
                                        $earning_description = sprintf( '%s %s: <a href="%s">%s</a>',
                                            $rank_types[$rank->post_type]['singular_name'],
                                            __( 'Requirement', 'gamipress' ),
                                            get_post_permalink( $rank->ID ),
                                            gamipress_get_post_field( 'post_title', $rank->ID )
                                        );
                                    }

                                } else if( in_array( $user_earning->post_type, gamipress_get_achievement_types_slugs() ) ) {
                                    // Achievement

                                    // Build a link to the achievement
                                    $earning_title = sprintf( '<a href="%s">%s</a>',
                                        get_post_permalink( $user_earning->post_id ),
                                        gamipress_get_post_field( 'post_title', $user_earning->post_id )
                                    );
                                    $earning_description = $achievement_types[$user_earning->post_type]['singular_name'];

                                } else if( in_array( $user_earning->post_type, gamipress_get_rank_types_slugs() ) ) {
                                    // Rank

                                    // Build a link to the rank
                                    $earning_title = sprintf( '<a href="%s">%s</a>',
                                        get_post_permalink( $user_earning->post_id ),
                                        gamipress_get_post_field( 'post_title', $user_earning->post_id )
                                    );
                                    $earning_description = $rank_types[$user_earning->post_type]['singular_name'];

                                }

                                $column_output = sprintf( '<strong class="gamipress-earning-title">%s</strong>'
                                    . '<br>'
                                    . '<span class="gamipress-earning-description">%s</span>',
                                    $earning_title,
                                    $earning_description
                                );

                                break;
                            case 'points':

                                $points = absint( $user_earning->points );

                                if( $points > 0 && isset( $points_types[$user_earning->points_type] ) ) {

                                    // Setup the output as %d point(s)
                                    $column_output = $points . ' ' . _n( $points_types[$user_earning->points_type]['singular_name'], $points_types[$user_earning->points_type]['plural_name'], $points );

                                    // For points deducts prepend a "-" sign
                                    if( $user_earning->post_type === 'points-deduct' ) {
                                        $column_output = '-' . $column_output;
                                    }

                                }

                                break;
                            case 'date':

                                $column_output = date_i18n( get_option( 'date_format' ), strtotime( $user_earning->date ) );

                                break;
                        }

                        /**
                         * Render earnings column
                         *
                         * @since 1.0.0
                         *
                         * @param string    $column_output  Default column output
                         * @param string    $column_name    The column name
                         * @param stdClass  $user_earning   The column name
                         * @param array     $template_args  Template received arguments
                         */
                        $column_output = apply_filters( 'gamipress_earnings_render_column', $column_output, $column_name, $user_earning, $a );
                        ?>

                        <td class="gamipress-earnings-col gamipress-earnings-col-<?php echo $column_name; ?>"><?php echo $column_output; ?></td>
                    <?php endforeach ?>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table><!-- .gamipress-earnings-table -->

        <?php // Pagination
        if( $a['pagination'] === 'yes' ) : ?>

            <div id="gamipress-earnings-pagination" class="gamipress-earnings-pagination navigation">
                <?php echo paginate_links( array(
                    'base'    => str_replace( 999999, '%#%', esc_url( get_pagenum_link( 999999 ) ) ),
                    'format'  => '?paged=%#%',
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'total'   => ceil( $a['query']->found_results / $a['limit'] )
                ) ); ?>
            </div>

        <?php endif; ?>

    <?php else : ?>

        <p id="gamipress-earnings-no-results"><?php echo __( 'You have not earned anything yet.', 'gamipress' ); ?></p>

    <?php endif; ?>

    <?php
    /**
     * After render earnings
     *
     * @since 1.0.0
     *
     * @param array $template_args Template received arguments
     */
    do_action( 'gamipress_after_render_earnings', $a ); ?>

</div>
