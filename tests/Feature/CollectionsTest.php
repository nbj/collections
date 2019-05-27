<?php
namespace Tests;

use stdClass;
use Countable;
use Exception;
use ArrayAccess;
use Nbj\Collection;
use RuntimeException;
use PHPUnit\Framework\TestCase;

class CollectionsTest extends TestCase
{
    /** @test */
    public function it_is_an_instance_of_array_access()
    {
        $collection = new Collection;

        $this->assertInstanceOf(ArrayAccess::class, $collection);
    }

    /** @test */
    public function it_behaves_like_an_array()
    {
        $collection = new Collection;
        $this->assertEquals(0, count($collection));

        $collection->add('some-item');
        $this->assertEquals(1, count($collection));

        $this->assertEquals('some-item', $collection[0]);

        unset($collection[0]);
        $this->assertEquals(0, count($collection));

        $collection['some-key'] = 'some-value';
        $this->assertEquals(1, count($collection));
        $this->assertTrue(isset($collection['some-key']));
    }

    /** @test */
    public function it_is_an_instance_of_countable()
    {
        $collection = new Collection;

        $this->assertInstanceOf(Countable::class, $collection);
    }

    /** @test */
    public function it_has_a_named_constructor_by_the_name_of_make()
    {
        $collection = Collection::make('some-item');

        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_can_have_new_items_pushed_to_it()
    {
        $collection = new Collection;
        $this->assertEmpty($collection);

        $collection->push('some_item');
        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_can_be_initialized_with_a_single_item()
    {
        $collection = new Collection('some-item');

        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_can_be_initialized_with_multiple_item()
    {
        $collection = new Collection([
            'some_item',
            'some_other_item',
            'some_third_item'
        ]);

        $this->assertCount(3, $collection);
    }

    /** @test */
    public function it_can_have_new_items_added_to_it()
    {
        $collection = new Collection;
        $this->assertEmpty($collection);

        $collection->add('some_item');
        $this->assertCount(1, $collection);
    }

    /** @test */
    public function it_knows_if_it_is_empty()
    {
        $collection = new Collection;

        $this->assertTrue($collection->isEmpty());

        $collection->add('some_item');
        $collection->add('some_other_item');

        $this->assertFalse($collection->isEmpty());
    }

    /** @test */
    public function it_knows_if_it_is_not_empty()
    {
        $collection = new Collection;

        $this->assertFalse($collection->isNotEmpty());

        $collection->add('some_item');
        $collection->add('some_other_item');

        $this->assertTrue($collection->isNotEmpty());
    }

    /** @test */
    public function it_can_convert_it_self_to_an_array()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $this->assertInstanceOf(Collection::class, $collection);

        $collection = $collection->toArray();

        $this->assertTrue(is_array($collection));
    }

    /** @test */
    public function it_can_convert_it_self_to_json()
    {
        $collection = new Collection([
            'name' => 'john',
            'age'  => 35
        ]);

        $json = $collection->toJson();

        $this->assertEquals('{"name":"john","age":35}', $json);
    }

    /** @test */
    public function it_knows_how_many_items_it_stores()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');

        $this->assertEquals(2, $collection->count());
    }

    /** @test */
    public function it_can_pop_off_the_last_item_of_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $this->assertCount(2, $collection);

        $item = $collection->pop();

        $this->assertCount(1, $collection);
        $this->assertEquals('some_other_item', $item);
    }

    /** @test */
    public function it_can_shift_off_the_first_item_of_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $this->assertCount(2, $collection);

        $item = $collection->shift();

        $this->assertCount(1, $collection);
        $this->assertEquals('some_item', $item);
    }

    /** @test */
    public function it_can_get_the_first_item_of_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $collection->add('some_third_item');

        $item = $collection->first();

        $this->assertCount(3, $collection);
        $this->assertEquals('some_item', $item);
    }

    /** @test */
    public function it_can_get_the_last_item_of_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $collection->add('some_third_item');

        $item = $collection->last();

        $this->assertCount(3, $collection);
        $this->assertEquals('some_third_item', $item);
    }

    /** @test */
    public function it_can_iterate_over_each_item_in_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $collection->add('some_third_item');

        $counter = 0;

        $collection->each(function ($item) use (&$counter) {
            $haystack = [
                'some_item',
                'some_other_item',
                'some_third_item'
            ];

            $this->assertTrue(in_array($item, $haystack));
            $counter++;
        });

        $this->assertEquals(3, $counter);
    }

    /** @test */
    public function it_can_iterate_over_each_item_in_the_collection_with_both_key_and_value()
    {
        $collection = new Collection([
            'name' => 'john',
            'age'  => 35,
            'sex'  => 'male'
        ]);

        $collection->each(function ($value, $key) use ($collection) {
            $this->assertTrue(array_key_exists($key, $collection->toArray()));
            $this->assertTrue(in_array($value, $collection->toArray()));
            $this->assertTrue($collection[$key] == $value);
        });
    }

    /** @test */
    public function it_can_map_over_each_item_in_the_collection()
    {
        $collection = new Collection;
        $collection->add('some_item');
        $collection->add('some_other_item');
        $collection->add('some_third_item');

        $newCollection = $collection->map(function ($item) {
            return $item . '_mapped';
        });

        $this->assertCount(3, $newCollection);
        $this->assertInstanceOf(Collection::class, $newCollection);

        $item = $newCollection->shift();
        $this->assertEquals('some_item_mapped', $item);

        $item = $newCollection->shift();
        $this->assertEquals('some_other_item_mapped', $item);

        $item = $newCollection->shift();
        $this->assertEquals('some_third_item_mapped', $item);
    }

    /** @test */
    public function it_can_filter_the_collection()
    {
        $collection = new Collection([
            'some_item',
            'some_other_item',
            'some_third_item'
        ]);

        $newCollection = $collection->filter(function ($item) {
            return $item == 'some_other_item';
        });

        $this->assertCount(1, $newCollection);

        $item = $newCollection->first();
        $this->assertEquals('some_other_item', $item);
    }

    /** @test */
    public function it_can_reject_items_in_the_collection()
    {
        $collection = new Collection([
            'some_item',
            'some_other_item',
            'some_third_item'
        ]);

        $newCollection = $collection->reject(function ($item) {
            return $item == 'some_other_item';
        });

        $this->assertCount(2, $newCollection);

        $item = $newCollection->shift();
        $this->assertEquals('some_item', $item);

        $item = $newCollection->shift();
        $this->assertEquals('some_third_item', $item);
    }

    /** @test */
    public function a_filter_accepts_both_key_and_value()
    {
        $collection = new Collection([
            'name' => 'john',
            'age'  => 35,
            'sex'  => 'male'
        ]);

        $newCollection = $collection->filter(function ($value, $key) {
            return $key == 'name' && $value == 'john';
        });

        $this->assertCount(1, $newCollection);

        $item = $newCollection->first();

        $this->assertEquals('john', $item);
    }

    /** @test */
    public function a_reject_accepts_both_key_and_value()
    {
        $collection = new Collection([
            'name' => 'john',
            'age'  => 35,
            'sex'  => 'male'
        ]);

        $newCollection = $collection->reject(function ($value, $key) {
            return $key == 'name' && $value == 'john';
        });

        $this->assertCount(2, $newCollection);

        $item = $newCollection->shift();
        $this->assertEquals(35, $item);

        $item = $newCollection->shift();
        $this->assertEquals('male', $item);
    }

    /** @test */
    public function it_takes_exception_to_passing_to_many_arguments_to_filter()
    {
        $collection = new Collection([
            'some_item',
            'some_other_item',
            'some_third_item'
        ]);

        try {
            $collection->filter(function ($value, $key, $thisShouldNotBeHere) {
                // This codes should never be executed
                // It is only here to satisfy IDEs
                // and tools like PHPStan
                $notRelevant = [
                    $value,
                    $key,
                    $thisShouldNotBeHere
                ];

                return $notRelevant;
            });
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertEquals('Too many parameters for filter() callback', $exception->getMessage());
        }
    }

    /** @test */
    public function it_takes_exception_to_passing_to_many_arguments_to_reject()
    {
        $collection = new Collection([
            'some_item',
            'some_other_item',
            'some_third_item'
        ]);

        try {
            $collection->reject(function ($value, $key, $thisShouldNotBeHere) {
                // This codes should never be executed
                // It is only here to satisfy IDEs
                // and tools like PHPStan
                $notRelevant = [
                    $value,
                    $key,
                    $thisShouldNotBeHere
                ];

                return $notRelevant;
            });
        } catch (Exception $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);
            $this->assertEquals('Too many parameters for reject() callback', $exception->getMessage());
        }
    }

    /** @test */
    public function it_can_check_every_item_against_a_truth_test()
    {
        $collectionA = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $resultA = $collectionA->every(function ($item) {
            return is_integer($item);
        });

        $this->assertTrue($resultA);

        $collectionB = new Collection([1, 'two', 3, 'four', 5, 'six', 7, 'eight', 9]);
        $resultB = $collectionB->every(function ($item) {
            return is_integer($item);
        });

        $this->assertFalse($resultB);
    }

    /** @test */
    public function it_can_reduce_a_collection_to_a_single_value()
    {
        $collectionA = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        // Implementation of sum() using reduce()
        $sum = $collectionA->reduce(function ($carry, $item) {
            $carry += $item;

            return $carry;
        }, 0);

        $this->assertEquals(45, $sum);
    }

    /** @test */
    public function it_can_summarize_a_collection_of_numbers()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $this->assertEquals(45, $collection->sum());
    }

    /** @test */
    public function it_can_summarize_a_collection_of_items_using_a_specific_field_in_array_form()
    {
        $collection = new Collection([
            [
                'name' => 'john',
                'age'  => 30
            ],
            [
                'name' => 'jane',
                'age'  => 26
            ],
            [
                'name' => 'jacob',
                'age'  => 44
            ],
        ]);

        $this->assertEquals(100, $collection->sum('age'));
    }

    /** @test */
    public function it_can_summarize_a_collection_of_items_using_a_specific_field_in_object_form()
    {
        $personA = new stdClass;
        $personA->name = 'john';
        $personA->age = 30;

        $personB = new stdClass;
        $personB->name = 'jane';
        $personB->age = 26;

        $personC = new stdClass;
        $personC->name = 'jacob';
        $personC->age = 44;

        $collection = new Collection([$personA, $personB, $personC]);

        $this->assertEquals(100, $collection->sum('age'));
    }

    /** @test */
    public function it_can_flatten_arrays() {
        $collection = new Collection([
            ['john@example.com'],
            ['jane@example.com'],
            ['jacob@example.com'],
        ]);

        $this->assertEquals([
            ['john@example.com'],
            ['jane@example.com'],
            ['jacob@example.com']
        ], $collection->toArray());

        $this->assertEquals([
            'john@example.com',
            'jane@example.com',
            'jacob@example.com'
        ], $collection->flatten()->toArray());
    }
}
