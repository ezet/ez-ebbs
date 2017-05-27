<?php

/**
 * @author     Lars Kristian Dahl <http://www.krisd.com>
 * @copyright  Copyright (c) 2011 Lars Kristian Dahl <http://www.krisd.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    SVN: $Id$
 */

namespace ebbs\models\dao;

use ebbs\models\User;

/**
 * Description of Userdao
 *
 * @author  Lars Kristian Dahl <http://www.krisd.com>
 */
class UserDAO extends \ebbs\models\dao\BaseDAO {

    // Set these manually if DAO and BO classes do not reflect the table name
    protected $_sql;
    protected $_table;
    protected $_model;
    protected $_pkey = 'UserId';

    /**
     * Inserts a new user and returns the newly created User as a domain object
     * @param User $user
     * @return <type>
     */
    public function insert(User $user) {
        $data = $user->toArray();
        $id = parent::_create($this->_table, $data);
        return $this->findById($id);
    }

    /**
     * Finds users matching a set of IDs
     * @param array $userids
     * @param <type> $limit
     * @return <type>
     */
    public function findManyById(array $userids, $limit = 10) {
        $users = array();
        foreach ($userids as $value) {
            $users[] = $value['UserId'];
        }
        $sql = 'SELECT * FROM `User` WHERE `UserId` IN ';
        $sql .= '(' . implode(', ', $users) . ')';
        $sql .= ' LIMIT ' . $limit;
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->_model);
    }

    /**
     * Finds a user by UserId
     * @param <type> $value
     * @param <type> $where
     * @return <type>
     */
    public function findById($value, $where='UserId') {
        return current($this->_retrieve($this->_table, $where, $value));
    }

    public function findBySearch($value) {
        $this->_select($this->_table);
        $value = '%' . $value . '%';
        $this->_sql .= " WHERE `Username` LIKE :search OR `BlogTitle` LIKE :search";
//        $sth = $this->rawQuery($sql, array('param' => $value));
        return $this->_rawQuery($this->_sql, array('search' => $value))->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->_model);
    }

    /**
     * Finds a user by username
     * @param <type> $value
     * @param <type> $where
     * @return <type>
     */
    public function findByUsername($value, $where='Username') {
        return current($this->_retrieve($this->_table, $where, $value));
    }

    /**
     * Finds blocked users
     * @param <type> $limit
     * @return <type>
     */
    public function findBlocked($limit=20) {
        return $this->_retrieve($this->_table, 'Blocked', 1);
    }

    /**
     * Finds all users who has had posts flagged atleast once
     * @param <type> $limit
     * @return <type>
     */
    public function findFlagged($limit=20) {
        $sql = "SELECT * FROM `$this->_table` WHERE `FlaggedCount` > 1 ORDER BY `FlaggedCount` DESC";
        return $this->_rawQuery($sql)->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->_model);
    }

    /**
     * Finds users sorted by provided field
     * @param <type> $orderby
     * @param <type> $limit
     * @param <type> $offset
     * @return <type>
     */
    public function findAllBy($orderby, $limit=20, $offset=0) {
        return $this->_retrieveList($this->_table, $limit, $offset, $orderby, 'DESC');
    }

    /**
     * Finds all users
     * @param <type> $limit
     * @param <type> $offset
     * @return <type>
     */
    public function findAll($limit=20, $offset=0) {
        return $this->_retrieveList($this->_table, $limit, $offset);
    }

    /**
     * Updates a user
     * @param User $user
     * @param <type> $field
     * @return <type>
     */
    public function update(User $user, $field='UserID') {
        $data = $user->toArray();
        $this->_update($this->_table, $data, $field, $user->UserId);
    }

    /**
     * Blocks a user
     * @param <type> $userid
     * @return <type>
     */
    public function block($userid) {
        return $this->_updateField($this->_table, 'UserId', $userid, 'Blocked', 1);
    }

    /**
     * Unblocks a user
     * @param <type> $userid
     * @return <type>
     */
    public function unblock($userid) {
        return $this->_updateField($this->_table, 'UserId', $userid, 'Blocked', 0);
    }

    /**
     * Increases the number of times a users post has been flagged
     * @param <type> $userid
     */
    public function updateFlaggedCount($userid) {
        $this->_updateCount($this->_table, 'UserId', $userid, 'FlaggedCount');
    }

    /**
     * Updates the logincount of a user
     * @param <type> $userid
     */
    public function updateLoginInfo($userid) {
        $this->_updateCount($this->_table, 'UserId', $userid, 'LoginCount');
    }

    /**
     * Deletes a user
     * @param <type> $value
     * @param <type> $field
     * @return <type>
     */
    public function delete($value, $field='UserId') {
        return parent::_delete($this->_table, $field, $value);
    }

}