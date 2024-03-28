<?php

use app\core\Application;
use app\models\User;

$user = User::getUserInfo(Application::$app->user->id);
?>
<script type="text/javascript">
  document.title = 'Quản lý người dùng';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Quản lý người dùng</h1>
        <a href="/admin/users/create" class="btn btn-success">Thêm người dùng</a>
      </header>
      <div class="panel-body">
        <table class="table table-striped table-hover dt-datatable">
          <thead>
            <tr>
              <th>Mã người dùng</th>
              <th>Tên</th>
              <th>Email</th>
              <th>Số điện thoại</th>
              <th>Vai trò</th>
              <th>Địa chỉ</th>
              <th class="no-sort"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($params['users'] as $userModel) { 
            ?>
              <tr>
                <td><?=$userModel->getId()?></td>
                <td><?=$userModel->getName()?></td>
                <td><?=$userModel->getEmail()?></td>
                <td><?=$userModel->getPhoneNumer()?></td>
                <td><?=$userModel->getRole()?></td>
                <td><?=$userModel->getAddress()?></td>
                <td>
                  <a class="fa fa-eye btn btn-info btn-sm" href="/admin/users/details?id=<?=$userModel->getId()?>"></a>
                  <a class="fa fa-pencil btn btn-warning btn-sm" href="/admin/users/edit?id=<?=$userModel->getId()?>"></a>
                  <a class="fa fa-trash btn btn-danger btn-sm" href="/admin/users/delete?id=<?=$userModel->getId()?>"></a>
                </td>
              </tr>
            <?php 
              }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>