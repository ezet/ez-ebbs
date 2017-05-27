<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\views;

use ebbs\views\Html;
use ebbs\libraries\Config;

/**
 * Various helper functions for the View
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class Viewhelper {

    public static function blogpostAdmin($user, $blogpost) {
        if ($blogpost->Deleted) {
            return;
        }
        ob_start();
        echo '<div class="admin-row">';
        if ($user->isOwner('Blogpost', $blogpost->BlogpostId)) {
            echo '<span class="meta-link">', Html::link('dashboard/editpost/' . $blogpost->BlogpostId, 'Edit'), '</span>';
            echo '<span class="meta-link">', Html::link('dashboard/deletepost/' . $blogpost->BlogpostId, 'Delete'), '<span>';
        } elseif ($user->isAdmin()) {
            echo '<span class="meta-link">', Html::link('admin/deletepost/' . $blogpost->BlogpostId, 'Delete'), '<span>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Inserts a flag link for comments
     * @param <type> $comment
     * @return <type>
     */
    public static function commentFlag($comment) {
        if ($comment->Deleted)
            return;
        ob_start();
        echo '<span class="meta-link">';
        if ($comment->Flagged) {
            echo "(This comment has been flagged for review)";
        } else {
            echo Html::link('comment/flag/' . $comment->CommentId, 'Flag');
        }
        echo '</span>';
        return ob_get_clean();
    }

    /**
     * Inserts a flag link for blogposts
     * @param <type> $blogpost
     * @return <type>
     */
    public static function blogpostFlag($blogpost) {
        if ($blogpost->Deleted)
            return;
        ob_start();
        echo '<span class="meta-link">';
        if ($blogpost->Flagged) {
            echo "(Flagged for review)";
        } else {
            echo Html::link('blog/flag/' . $blogpost->BlogpostId . '/' . $blogpost->UserId, 'Flag');
        }
        echo '</span>';
        return ob_get_clean();
    }

    /**
     * Inserts a created tag
     * @param <type> $model
     * @return <type>
     */
    public static function created($model) {
        return '<span class="date">Posted ' . Html::e($model->Created) . '</span>';
    }

    /**
     * Inserts a modified tag
     * @param <type> $model
     * @return <type>
     */
    public static function modified($model) {
        return '<span class="date">Last modified ' . Html::e($model->Modified) . '</span>';
    }

    /**
     * Inserts a facebookLike button
     */
    public static function fbLike($post) {
        ob_start();
        echo '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
        echo '<span class="meta-link">';
        echo '<fb:like href="' . __BASE_URL . '/blog/viewpost/' . $post->BlogpostId . '" layout="button_count" show_faces="false" width="20" font="verdana"></fb:like>';
        echo '</span>';
        return ob_get_clean();
    }

    /**
     * Inserts the profile image, scaled to desired size
     * @param <type> $user
     * @return <type>
     */
    public static function profileImg($user, $size=170) {
        $img = __BASE_URL . '/' . Config::get('profileimg_path') . $user->ProfileImage;
        return '<img ' . self::_imageResize($img, $size) . ' src="' . $img . '"/>';
    }

    /**
     * "Resizes" an image by adjusting the html height/width attributes.
     * It's good enough for profile images.
     * Content images use the more advanced CKEditor image manager
     * @param <type> $img
     * @param <type> $target
     * @return <type>
     */
    private static function _imageResize($img, $target) {
        $size = getimagesize($img);
        $width = $size[0];
        $height = $size[1];

// find the most significant constraint
        if ($width > $height) {
            $percentage = ($target / $width);
        } else {
            $percentage = ($target / $height);
        }

//      get the new value and applies the percentage
        $width = round($width * $percentage);
        $height = round($height * $percentage);

        return "width=\"$width\" height=\"$height\"";
    }

}