<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Access {
    var $_group = 'misc',
        $_icon = 'configure.png',
        $_title,
        $_sort_order = 0,
        $_subgroups;

    function getUserLevels($id) {
      global $osC_Database;

      $modules = array();

      $Qaccess = $osC_Database->query('select module from :table_administrators_access where administrators_id = :administrators_id');
      $Qaccess->bindTable(':table_administrators_access', TABLE_ADMINISTRATORS_ACCESS);
      $Qaccess->bindInt(':administrators_id', $id);
      $Qaccess->execute();

      while ( $Qaccess->next() ) {
        $modules[] = $Qaccess->value('module');
      }

      if ( in_array('*', $modules) ) {
        $modules = array();

        $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/access');
        $osC_DirectoryListing->setIncludeDirectories(false);

        foreach ($osC_DirectoryListing->getFiles() as $file) {
          $modules[] = substr($file['name'], 0, strrpos($file['name'], '.'));
        }
      }

      return $modules;
    }

    function getLevels() {
      global $osC_Language;

      $access = array();

      foreach ( $_SESSION['admin']['access'] as $module ) {
        if ( file_exists('includes/modules/access/' . $module . '.php') ) {
          $module_class = 'osC_Access_' . ucfirst($module);

          if ( !class_exists( $module_class ) ) {
            $osC_Language->loadConstants('modules/access/' . $module . '.php');
            include('includes/modules/access/' . $module . '.php');
          }

          $module_class = new $module_class();

          $data = array('module' => $module,
                        'icon' => $module_class->getIcon(),
                        'title' => $module_class->getTitle(),
                        'subgroups' => $module_class->getSubGroups());

          if ( !isset( $access[$module_class->getGroup()][$module_class->getSortOrder()] ) ) {
            $access[$module_class->getGroup()][$module_class->getSortOrder()] = $data;
          } else {
            $access[$module_class->getGroup()][] = $data;
          }
        }
      }

      return $access;
    }

    function getModule() {
      return $this->_module;
    }

    function getGroup() {
      return $this->_group;
    }

    function getGroupTitle($group) {
      global $osC_Language;

      if ( !defined('ACCESS_GROUP_' . strtoupper( $group ) . '_TITLE') ) {
        $osC_Language->loadConstants( 'modules/access/groups/' . $group . '.php' );
      }

      if ( defined('ACCESS_GROUP_' . strtoupper( $group ) . '_TITLE') ) {
        return constant('ACCESS_GROUP_' . strtoupper( $group ) . '_TITLE');
      }

      return $group;
    }

    function getIcon() {
      return $this->_icon;
    }

    function getTitle() {
      return $this->_title;
    }

    function getSortOrder() {
      return $this->_sort_order;
    }

    function getSubGroups() {
      return $this->_subgroups;
    }

    function hasAccess($module = null) {
      if ( empty($module) ) {
        $module = $this->_module;
      }

      return !file_exists( 'includes/modules/access/' . $module . '.php' ) || in_array( $module, $_SESSION['admin']['access'] );
    }
  }
?>
