# WP Content Framework (DB module)

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

[WP Content Framework](https://github.com/wp-content-framework/core) のモジュールです。

# 要件
- PHP 5.6 以上
- WordPress 3.9.3 以上

# インストール

``` composer require wp-content-framework/db ```  

## 依存モジュール
* [core](https://github.com/wp-content-framework/core)  

## 基本設定
- configs/config.php  

|設定値|説明|
|---|---|
|db_version|DBのバージョン \[default = 0.0.1]|
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
            'unsigned' => false,          // optional [default = false]
            'null'     => true,           // optional [default = true]
            'default'  => null,           // optional [default = null]
            'comment'  => '',             // optional
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
),
```

設定を更新したら configs/config.php の db_version も更新します。  
自動でテーブルの追加・更新が行われます。  
データの取得・挿入・更新・削除は以下のように行います。
```
// 取得
$this->app->db->select( 'test', array(
	'id'         => array( 'in', array( 1, 2, 3 ) ),
	'value1'     => array( 'like', 'tes%' ),
	'created_at' => array( '<', '2018-06-03' ),
	'value2'     => null,
	'value3'     => 3,
) );

// 挿入
$this->app->db->insert( 'test', array(
    'name'   => 'aaa',
    'value1' => 'bbb',
    'value3' => 100,
) );

// 更新
$this->app->db->update( 'test', array(
    'value2' => 'ccc',
), array(
    'id' => 4,
) );

// 削除
$this->app->db->delete( 'test', array(
    'id' => 4,
) );
```
select 以外は 内部でWordPress標準の関数を使用しているため、  
条件の指定の仕方は 'key' => 'value' (key = value) のみ可能です。  
select の条件指定はライブラリ側で構築しており、  
key = value  
```
key' => 'value'
```
key in ( val1, val2, val3 )
```
'key' => array( 'in', array( val1, val2, val3 ) )  
```
key like '%value%'
```
'key' => array( 'like', '%value%' )
```
などが指定可能です。

# Author

[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)
