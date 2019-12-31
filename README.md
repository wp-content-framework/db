# WP Content Framework (DB module)

[![CI Status](https://github.com/wp-content-framework/db/workflows/CI/badge.svg)](https://github.com/wp-content-framework/db/actions)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

[WP Content Framework](https://github.com/wp-content-framework/core) のモジュールです。

# 要件
- PHP 5.6 以上
- WordPress 3.9.3 以上

# インストール

``` composer require wp-content-framework/db ```

## 基本設定
- configs/config.php

|設定値|説明|
|---|---|
|default_delete_rule|デフォルトの削除動作を指定（physical or logical \[default = physical]）|

- configs/db.php

設定例：
```
// テーブル名 => 設定
'test' => array(

    // primary key 設定
    'id'      => 'test_id',     // optional [default = $table_name . '_id']

    // カラム 設定
    'columns' => array(

        // 論理名 => 設定
        'name'   => array(
            'name'     => 'name_test',     // optional (物理名)
            'type'     => 'VARCHAR(32)',   // required
            'unsigned' => false,           // optional [default = false]
            'null'     => true,            // optional [default = true]
            'default'  => null,            // optional [default = null]
            'comment'  => '',              // optional
        ),
        'value1' => array(
            'type'    => 'VARCHAR(32)',
            'null'    => false,
            'default' => 'test',
        ),
        'value2' => array(
            'type'    => 'VARCHAR(32)',
            'comment' => 'aaaa',
        ),
        'value3' => array(
            'type'    => 'INT(11)',
            'null'    => false,
            'comment' => 'bbb',
        ),
    ),

    // index 設定
    'index'   => array(
        // key index
        'key'    => array(
            'name' => array( 'name' ),
        ),

        // unique index
        'unique' => array(
            'value' => array( 'value1', 'value2' ),
        ),
    ),

    // 論理削除 or 物理削除
    'delete'  => 'logical', // physical or logical [default = physical]

    // コメント
    'comment' => 'test,
),
```

プラグインのバージョンが変更されたとき またはキャッシュを削除することで 自動でテーブルの追加・更新が行われます。
default の途中変更に関しては文字列のみ対応しています。 [詳細](https://github.com/wp-content-framework/db/issues/25#issuecomment-492293617)
データの取得・挿入・更新・削除はLaravelのDB操作と同じように行うことができます。
```
// 取得
$this->table( 'test' )
     ->where_integer_in_raw( 'id', [ 1, 2, 3 ] )
     ->where( 'value1', 'like', 'tes%' )
     ->where( 'created_at', '<', '2020-01-01' )
     ->where_null( 'value2' )
     ->where( 'value3', 3 )
     ->get();

// 取得（WordPressのテーブル）
$this->wp_table( 'posts' )
     ->where( 'post_type', 'page' )
     ->order_by( 'post_modified' )
     ->limit( 10 )
     ->get();

// 取得（結合）
$this->table( 'test', 't' )
     ->alias_join( 'test2', 't2', 't.id', 't2.test_id' )
     ->alias_join_wp( 'posts', 'p', 't.value3', 'p.ID' )
     ->where_in( 't.value1', [ 'test1', 'test2', 'test3' ] )
     ->get();

// 取得（単体）
$this->table( 'test' )
     ->where( 'value1', 'test1' )
     ->row();

 // 取得（ID単体）
$this->table( 'test' )
     ->find( 10 );

// 挿入
$this->table( 'test' )
     ->insert( [
         'name'   => 'aaa',
         'value1' => 'bbb',
         'value3' => 100,
     ] );

// 挿入（一括）
$this->table( 'test' )
     ->insert( [
         [
             'name'   => 'aaa1',
             'value1' => 'bbb1',
             'value3' => 100,
         ],
         [
             'name'   => 'aaa2',
             'value1' => 'bbb2',
             'value3' => 200,
         ],
         [
             'name'   => 'aaa3',
             'value1' => 'bbb3',
             'value3' => 300,
         ],

// 更新
$this->table( 'test' )
     ->where( 'id', 4 )
     ->update( [
         'value2' => 'ccc',
     ] );

// 削除
$this->table( 'test' )
     ->where( 'value1', 'test' )
     ->delete();

// 削除（ID）
$this->table( 'test' )
     ->delete( 4 );
```

# Author

[GitHub (Technote)](https://github.com/technote-space)
[Blog](https://technote.space)
