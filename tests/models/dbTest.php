<?php
/**
 * WP_Framework_Db Models Define Test
 *
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Db\Tests\Models;

use Phake;
use WP_Framework_Db\Tests\TestCase;

require_once __DIR__ . DS . 'misc' . DS . 'db.php';

/**
 * Class DbTest
 * @package WP_Framework_Db\Tests\Models
 * @group wp_framework
 * @group models
 */
class DbTest extends TestCase {

	/**
	 * @var Misc\Db $_db
	 */
	private static $_db;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$_db = Misc\Db::get_instance( static::$app );
		Phake::when( static::$app )->__get( 'db' )->thenReturn( static::$_db );
		static::$_db->drop( 'technote_test_table1' );
		static::$_db->drop( 'technote_test_table2' );
		static::$_db->setup( 'technote_test_table1', [
			'id'      => 'test_id',
			'columns' => [
				'value1' => [
					'type'    => 'VARCHAR(32)',
					'null'    => false,
					'default' => 'value1',
				],
				'value2' => [
					'type'    => 'INT(11)',
					'null'    => false,
					'default' => 2,
				],
				'value3' => [
					'type' => 'VARCHAR(32)',
				],
			],
			'index'   => [
				'key' => [
					'value1' => [ 'value1' ],
				],
			],
			'delete'  => 'logical',
		] );
		static::$_db->setup( 'technote_test_table2', [
			'columns' => [
				'value1' => [
					'type'    => 'VARCHAR(32)',
					'null'    => false,
					'default' => 'value1',
				],
				'value2' => [
					'type'    => 'INT(11)',
					'null'    => false,
					'default' => 2,
				],
				'value3' => [
					'type' => 'VARCHAR(32)',
				],
			],
			'index'   => [
				'key'    => [
					'value1' => [ 'value1' ],
				],
				'unique' => [
					'value' => [ 'value1', 'value2' ],
				],
			],
			'delete'  => 'physical',
		] );
	}

	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
		static::$_db->drop( 'technote_test_table1' );
		static::$_db->drop( 'technote_test_table2' );
	}

	public function test_table_not_exists() {
		$this->assertFalse( static::$_db->exists( 'technote_test_table1' ) );
		$this->assertFalse( static::$_db->exists( 'technote_test_table2' ) );
	}

	/**
	 * @depends test_table_not_exists
	 */
	public function test_table_update() {
		$results = static::$_db->_table_update( 'technote_test_table1' );
		$this->assertNotEmpty( $results );
		$results = static::$_db->_table_update( 'technote_test_table2' );
		$this->assertNotEmpty( $results );
	}

	/**
	 * @depends test_table_update
	 */
	public function test_table_exists() {
		$this->assertTrue( static::$_db->exists( 'technote_test_table1' ) );
		$this->assertTrue( static::$_db->exists( 'technote_test_table2' ) );
	}

	/**
	 * @depends test_table_exists
	 */
	public function test_column_check1() {
		$columns = static::$_db->columns( 'technote_test_table1' );
		$columns = array_combine( array_map( function ( $d ) {
			return $d['Field'];
		}, $columns ), $columns );
		$this->assertArrayHasKey( 'test_id', $columns );
		$this->assertArrayHasKey( 'value1', $columns );
		$this->assertArrayHasKey( 'value2', $columns );
		$this->assertArrayHasKey( 'value3', $columns );
		$this->assertArrayHasKey( 'deleted_at', $columns );
		$this->assertArrayHasKey( 'deleted_by', $columns );
	}

	/**
	 * @depends test_column_check1
	 */
	public function test_column_check2() {
		$columns = static::$_db->columns( 'technote_test_table2' );
		$columns = array_combine( array_map( function ( $d ) {
			return $d['Field'];
		}, $columns ), $columns );
		$this->assertArrayHasKey( 'technote_test_table2_id', $columns );
		$this->assertArrayHasKey( 'value1', $columns );
		$this->assertArrayHasKey( 'value2', $columns );
		$this->assertArrayHasKey( 'value3', $columns );
		$this->assertArrayNotHasKey( 'deleted_at', $columns );
		$this->assertArrayNotHasKey( 'deleted_by', $columns );
	}

	/**
	 * @depends test_column_check2
	 */
	public function test_table_update_same() {
		$results = static::$_db->_table_update( 'technote_test_table1' );
		$this->assertEmpty( $results );
		$results = static::$_db->_table_update( 'technote_test_table2' );
		$this->assertEmpty( $results );
	}

	/**
	 * @depends test_table_update_same
	 */
	public function test_table_update_define() {
		static::$_db->setup( 'technote_test_table2', [
			'columns' => [
				'value1' => [
					'type'    => 'VARCHAR(32)',
					'null'    => false,
					'default' => 'value1',
				],
				'value2' => [
					'type'    => 'INT(11)',
					'null'    => false,
					'default' => '2',
				],
				'value3' => [
					'type' => 'VARCHAR(32)',
				],
				'value4' => [
					'type' => 'INT(11)',
				],
			],
			'index'   => [
				'key'    => [
					'value1' => [ 'value1' ],
				],
				'unique' => [
					'value' => [ 'value1', 'value2' ],
				],
			],
			'delete'  => 'physical',
		] );
		$results = static::$_db->_table_update( 'technote_test_table2' );
		$this->assertNotEmpty( $results );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_column_check3() {
		$columns = static::$_db->columns( 'technote_test_table2' );
		$columns = array_combine( array_map( function ( $d ) {
			return $d['Field'];
		}, $columns ), $columns );
		$this->assertArrayHasKey( 'value4', $columns );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_insert() {
		$this->assertEquals( 1, static::$_db->insert( 'technote_test_table1', [
			'value1' => 'text1',
			'value2' => 1,
			'value3' => 'text3',
		] ) );
		$this->assertEquals( 1, static::$_db->insert( 'technote_test_table2', [
			'value3' => 'text1',
			'value4' => 1,
		] ) );
		$this->assertEquals( 1, static::$_db->insert( 'technote_test_table2', [
			'value2' => 10,
			'value3' => 'text2',
			'value4' => 2,
		] ) );
		$this->assertEquals( 1, static::$_db->insert( 'technote_test_table2', [
			'value2' => 20,
			'value3' => 'text2',
			'value4' => null,
		] ) );
		$this->assertEquals( 1, static::$_db->insert( 'technote_test_table2', [
			'value2' => 30,
			'value3' => 'text2',
			'value4' => 2,
		] ) );

		$this->assertEquals( 2, static::$_db->builder()->table( 'technote_test_table1' )->insert( [
			'value1' => 'text10',
			'value2' => 10,
			'value3' => 'text30',
		] ) );
		$this->assertEquals( 5, static::$_db->builder()->table( 'technote_test_table2' )->insert( [
			'value2' => 0,
			'value3' => 'text10',
			'value4' => 10,
		] ) );
		$this->assertEquals( 6, static::$_db->builder()->table( 'technote_test_table2' )->insert( [
			'value2' => 100,
			'value3' => 'text20',
			'value4' => 20,
		] ) );
		$this->assertEquals( 7, static::$_db->builder()->table( 'technote_test_table2' )->insert( [
			'value2' => 200,
			'value3' => 'text20',
			'value4' => null,
		] ) );
		$this->assertEquals( 8, static::$_db->builder()->table( 'technote_test_table2' )->insert( [
			'value2' => 300,
			'value3' => 'text20',
			'value4' => 2,
		] ) );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_update() {
		$this->assertEquals( 1, static::$_db->update( 'technote_test_table2', [
			'value3' => 'text3',
			'value4' => 3,
		], [
			'id' => 1,
		] ) );
		$this->assertEquals( 0, static::$_db->update( 'technote_test_table2', [
			'value3' => 'text4',
			'value4' => 4,
		], [
			'id' => 10,
		] ) );
		$this->assertEquals( 1, static::$_db->update( 'technote_test_table2', [
			'value4' => null,
		], [
			'id' => 4,
		] ) );

		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table2' )->where( 'id', 5 )->update( [
			'value3' => 'text30',
			'value4' => 30,
		] ) );
		$this->assertEquals( 0, static::$_db->builder()->table( 'technote_test_table2' )->where( 'id', 10 )->update( [
			'value3' => 'text40',
			'value4' => 40,
		] ) );
		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table2' )->where( 'id', 8 )->update( [
			'value4' => null,
		] ) );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_select() {
		$results = static::$_db->select( 'technote_test_table2', [
			'id' => 1,
		] );
		$this->assertNotEmpty( $results );
		$this->assertCount( 1, $results );
		$result = reset( $results );
		$this->assertArrayHasKey( 'id', $result );
		$this->assertArrayHasKey( 'value3', $result );
		$this->assertArrayHasKey( 'value4', $result );
		$this->assertEquals( 'text3', $result['value3'] );
		$this->assertEquals( 3, $result['value4'] );

		$results = static::$_db->select( 'technote_test_table2', [
			'id' => [ 'in', [ 3, 4 ] ],
		] );
		$this->assertCount( 2, $results );
		$this->assertNull( $results[0]['value4'] );
		$this->assertNull( $results[1]['value4'] );

		$result = static::$_db->builder()->table( 'technote_test_table2' )->find( 5 );
		$this->assertArrayHasKey( 'id', $result );
		$this->assertArrayHasKey( 'value3', $result );
		$this->assertArrayHasKey( 'value4', $result );
		$this->assertEquals( 'text30', $result['value3'] );
		$this->assertEquals( 30, $result['value4'] );

		$results = static::$_db->builder()->table( 'technote_test_table2' )->where_integer_in_raw( 'id', [ 7, 8 ] )->get();
		$this->assertCount( 2, $results );
		$this->assertNull( $results[0]['value4'] );
		$this->assertNull( $results[1]['value4'] );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_select2() {
		$results = static::$_db->select( 'technote_test_table2', [
			'id' => 10,
		] );
		$this->assertEmpty( $results );

		$results = static::$_db->builder()->table( 'technote_test_table2' )->find( 10 );
		$this->assertEmpty( $results );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_count() {
		$this->assertEquals( 2, static::$_db->select_count( 'technote_test_table1' ) );
		$this->assertEquals( 8, static::$_db->select_count( 'technote_test_table2' ) );
		$this->assertEquals( 1, static::$_db->select_count( 'technote_test_table1', '*', [ 'value2' => 1 ] ) );

		$this->assertEquals( 2, static::$_db->builder()->table( 'technote_test_table1' )->count() );
		$this->assertEquals( 8, static::$_db->builder()->table( 'technote_test_table2' )->count() );
		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table1' )->where( 'value2', 1 )->count() );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_chunk() {
		$count = 0;
		static::$_db->builder()->table( 'technote_test_table2' )->chunk( 2, function ( $results ) use ( &$count ) {
			$this->assertCount( 2, $results );
			$count++;
		} );
		$this->assertEquals( 8 / 2, $count );

		$count = 0;
		static::$_db->builder()->table( 'technote_test_table2' )->chunk( 2, function ( $results ) use ( &$count ) {
			$this->assertCount( 2, $results );
			$count++;

			return false;
		} );
		$this->assertEquals( 1, $count );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_each() {
		$count = 0;
		static::$_db->builder()->table( 'technote_test_table2' )->each( 2, function () use ( &$count ) {
			$count++;
		} );
		$this->assertEquals( 8, $count );

		$count = 0;
		static::$_db->builder()->table( 'technote_test_table2' )->each( 2, function () use ( &$count ) {
			$count++;

			return false;
		} );
		$this->assertEquals( 1, $count );
	}

	/**
	 * @depends test_table_update_define
	 */
	public function test_delete() {
		$this->assertEquals( 1, static::$_db->delete( 'technote_test_table1', [
			'id' => 1,
		] ) );
		$this->assertEquals( 0, static::$_db->delete( 'technote_test_table1', [
			'id' => 1,
		] ) );
		$this->assertEquals( 1, static::$_db->delete( 'technote_test_table2', [
			'id' => 1,
		] ) );
		$this->assertEquals( 1, static::$_db->delete( 'technote_test_table2', [
			'id' => 2,
		] ) );
		$this->assertEquals( 1, static::$_db->delete( 'technote_test_table2', [
			'id' => 3,
		] ) );
		$this->assertEquals( 1, static::$_db->delete( 'technote_test_table2', [
			'id' => 4,
		] ) );
		$this->assertEquals( 0, static::$_db->delete( 'technote_test_table2', [
			'id' => 10,
		] ) );

		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table1' )->delete( 2 ) );
		$this->assertEquals( false, static::$_db->builder()->table( 'technote_test_table1' )->delete( 2 ) );
		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table2' )->delete( 5 ) );
		$this->assertEquals( 1, static::$_db->builder()->table( 'technote_test_table2' )->delete( 6 ) );
		$this->assertEquals( false, static::$_db->builder()->table( 'technote_test_table2' )->delete( 10 ) );
		$this->assertEquals( 2, static::$_db->builder()->table( 'technote_test_table2' )->where_integer_in_raw( 'id', [ 7, 8 ] )->delete() );
	}

	/**
	 * @depends test_delete
	 */
	public function test_select3() {
		$results = static::$_db->select( 'technote_test_table1', [
			'id' => 1,
		] );
		$this->assertEmpty( $results );
		$results = static::$_db->select( 'technote_test_table2', [
			'id' => 1,
		] );
		$this->assertEmpty( $results );

		$results = static::$_db->builder()->table( 'technote_test_table1' )->where( 'id', 1 )->get();
		$this->assertEmpty( $results );
		$result = static::$_db->builder()->table( 'technote_test_table2' )->find( 1 );
		$this->assertNull( $result );
	}
}
