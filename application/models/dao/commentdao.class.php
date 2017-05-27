<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models\dao;

use ebbs\models\Comment;

/**
 * Description of Commentdao
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class CommentDAO extends \ebbs\models\dao\BaseDAO {

    /**
     * Inserts a new comment
     * @param Comment $comment
     * @return <type>
     */
    public function insert(Comment $comment) {
        $comment->Created = date('YmdHis', time());
        $data = $comment->toArray();
        return $this->_create($this->_table, $data);
    }

    /**
     * Finds a comment by its ID
     * @param <type> $value
     * @param <type> $field
     * @return <type>
     */
    public function findById($value, $field='CommentId') {
        return current($this->_retrieve($this->_table, $field, $value));
    }

    /**
     * Finds a comment by its BlogpostId
     * @param <type> $value
     * @param <type> $where
     * @param <type> $limit
     * @return <type>
     */
    public function findByPostId($value, $where='BlogpostId', $limit = 50) {
        return $this->_retrieve($this->_table, $where, $value, $limit, 0, 'Created', 'DESC');
    }

    /**
     * Find IDs of blogposts with the most comments
     * @param <type> $limit
     * @return <type>
     */
    public function findMostCommentedIds($limit = 20) {
        $sql = 'SELECT `BlogpostId`, COUNT(`BlogpostId`) as CommentCount
            FROM `Comment`
            GROUP BY `BlogpostId`
            ORDER BY 2 DESC
            LIMIT 10';
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Finds flagged comments
     * @param <type> $limit
     * @return <type>
     */
    public function findFlagged($limit = 20) {
        return $this->_retrieve($this->_table, 'Flagged', 1, $limit);
    }

    /**
     * Flags a comment
     * @param <type> $commentid
     */
    public function flag($commentid) {
        $this->_updateField($this->_table, 'CommentId', $commentid, 'Flagged', 1);
    }

    /**
     * Unflags a comment
     * @param <type> $commentid
     */
    public function unflag($commentid) {
        $this->_updateField($this->_table, 'CommentId', $commentid, 'Flagged', 0);
    }

    /**
     * Simulates a deletion by replacing text, setting the deleted flag and setting the BlogpostId to 0;
     * @param <type> $postid
     * @param <type> $msg
     */
    public function fakedelete($commentid, $msg) {
        $sql = "UPDATE `$this->_table` SET `Deleted`=1, `Text`='$msg' WHERE `CommentId`=?";
        $this->_rawQuery($sql, array($commentid));
        $this->unflag($commentid);
    }

    /**
     * Deletes a comment
     * @param <type> $value
     * @param <type> $field
     */
    public function delete($value, $field='CommentId') {
        $this->_delete($this->_table, $value, $field);
    }

    public function countByPostId() {
        $sql = "SELECT COUNT(BlogpostId) FROM $this->_table WHERE BlogpostId=1 GROUP BY BlogpostId";
        $sth = $this->_rawQuery($sql)->fetchAll();
        var_dump($sth);
    }

}