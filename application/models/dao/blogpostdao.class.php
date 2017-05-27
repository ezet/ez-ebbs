<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models\dao;

use ebbs\models\Blogpost;

/**
 * Description of Blogdao
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class BlogpostDAO extends \ebbs\models\dao\BaseDAO {

    /**
     * Inserts a new blogpost, returns its ID
     * @param Blogpost $post
     * @return <type>
     */
    public function insert(Blogpost $post) {
        $data = $post->toArray();
        $id = parent::_create($this->_table, $data);
        return $this->lastInsertId();
    }

    /**
     * Finds a blogpost by ID
     * @param <type> $value
     * @param <type> $where
     * @return <type>
     */
    public function FindById($value, $where='BlogpostId') {
        return current($this->_retrieve($this->_table, $where, $value));
    }

    /**
     * Finds many blogposts matching a set of IDs
     * @param array $postids
     * @param <type> $limit
     * @return <type>
     */
    public function findManyById(array $postids, $limit = 10) {
        $posts = array();
        foreach ($postids as $value) {
            $posts[] = $value['BlogpostId'];
        }
        $sql = 'SELECT * FROM `Blogpost` WHERE `BlogpostId` IN ';
        $sql .= '(' . implode(', ', $posts) . ')';
        $sql .= ' LIMIT ' . $limit;
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->_model);
    }

    /**
     * Finds the IDs of the users with highest total views across all blogposts
     * @param <type> $limit
     * @return <type>
     */
    public function findTopBloggerIds($limit = 20) {
        $sql = 'SELECT `UserId`, SUM(`ViewCount`) as TotalViews
           FROM `Blogpost`
           GROUP BY `UserId`
           ORDER BY TotalViews DESC
           LIMIT 10';
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Finds the blogposts with highest viewcount
     * @param <type> $limit
     * @return <type>
     */
    public function findByViews($limit = 20) {
        return $this->_retrieveList($this->_table, $limit, 0, 'ViewCount', 'DESC');
    }

    public function findByComments() {
        $sql = 'SELECT b.* as Comment
             FROM Blogpost AS b
             LEFT JOIN Comment AS c
             ON b.BlogpostId = c.BlogpostId
             GROUP BY c.BlogpostId
             ORDER BY COUNT(c.CommentId) DESC
             LIMIT 10';
//        $res = $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_ASSOC);
//        var_dump($res);
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->_model);
    }

    /**
     * Finds blogposts sorted by date
     * @param <type> $limit
     * @return <type>
     */
    public function findByDate($limit = 20) {
        return $this->_retrieveList($this->_table, $limit, 0, 'Created', 'DESC');
    }

    /**
     * Finds all flagged posts
     * @param <type> $limit
     * @return <type>
     */
    public function findFlagged($limit = 20) {
        return $this->_retrieve($this->_table, 'Flagged', 1, $limit);
    }

    /**
     * Finds all posts associated with a specific User
     * @param <type> $value
     * @param <type> $where
     * @return <type>
     */
    public function getBlog($value, $where='UserId') {
        return $this->_retrieve($this->_table, $where, $value, 20);
    }

    /**
     * Updates a blogpost row
     * @param Blogpost $post
     */
    public function update(Blogpost $post) {
        $id = $post->BlogpostId;
//        unset($post->BlogpostId);
        $data = $post->toArray();
        $res = $this->_update($this->_table, $data, 'BlogpostId', $post->BlogpostId);
    }

    /**
     * Updates the viewcount of a blogpost
     * @param <type> $postid
     */
    public function updateViewCount($postid) {
        $this->_updateCount($this->_table, 'BlogpostId', $postid, 'Viewcount');
    }

    /**
     * Simulates a deletetion by replacing the blogtext, setting the deleted flag and changing UserId to 0
     * @param <type> $postid
     * @param <type> $msg
     */
    public function fakedelete($postid, $msg) {
        $sql = "UPDATE `$this->_table` SET `UserId`=0, `Deleted`=1, `Text`='$msg' WHERE `BlogpostId`=?";
        $this->_rawQuery($sql, array($postid));
        $this->unflag($postid);
    }

    /**
     * Sets the Flagged flag on a post
     * @param <type> $postid
     */
    public function flag($postid) {
        $this->_updateField($this->_table, 'BlogpostId', $postid, 'Flagged', 1);
    }

    /**
     * Removes the Flagged flag on a post
     * @param <type> $postid
     */
    public function unflag($postid) {
        $this->_updateField($this->_table, 'BlogpostId', $postid, 'Flagged', 0);
    }

    /**
     * Deletes a post
     * @param <type> $postid
     * @return <type>
     */
    public function delete($postid) {
        return $this->_delete($this->_table, 'BlogpostId', $postid);
    }

}