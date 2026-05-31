<?php
if (post_password_required()) return;
?>

<div class="comments-section" id="comments">

    <?php if (have_comments()) : ?>

    <h2 class="comments-title">
        <?php
        $count = get_comments_number();
        echo esc_html(
            sprintf(
                _n('%s comment', '%s comments', $count, 'vt-folio'),
                number_format_i18n($count)
            )
        );
        ?>
    </h2>

    <ol class="comment-list">
        <?php
        wp_list_comments([
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 44,
            'callback'    => 'vt_comment',
        ]);
        ?>
    </ol>

    <?php the_comments_pagination([
        'prev_text' => '<span aria-hidden="true">&larr;</span><span class="screen-reader-text">' . __('Previous comments', 'vt-folio') . '</span>',
        'next_text' => '<span aria-hidden="true">&rarr;</span><span class="screen-reader-text">' . __('Next comments', 'vt-folio') . '</span>',
    ]); ?>

    <?php endif; ?>

    <?php
    comment_form([
        'title_reply'   => __('Leave a comment', 'vt-folio'),
        'label_submit'  => __('Post comment',    'vt-folio'),
        'comment_notes_before' => '',
    ]);
    ?>

</div>
