<?php

namespace Propel\Bundle\PropelBundle\Tests\ArgumentResolver;

use Propel\Bundle\PropelBundle\ArgumentResolver\PropelValueResolver;
use Propel\Bundle\PropelBundle\Attribute\MapModel;
use Propel\Bundle\PropelBundle\Tests\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionWrapper;
use Propel\Runtime\Propel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PropelValueResolverTest extends TestCase
{
    private ConnectionWrapper $con;

    private PropelValueResolver $resolver;

    protected function setUp(): void
    {
        if (!class_exists(MyBook::class)) {
            QuickBuilder::buildSchema(<<<XML
<database name="value_resolver" namespace="Propel\Bundle\PropelBundle\Tests\ArgumentResolver" defaultIdMethod="native">
    <table name="my_book">
        <column name="id" type="integer" primaryKey="true" />
        <column name="name" type="varchar" size="255" />
        <column name="slug" type="varchar" size="255" />
        <column name="author_slug" type="varchar" size="255" />
        <column name="my_author_id" type="integer" required="true" />

        <foreign-key foreignTable="my_author" onDelete="CASCADE" onUpdate="CASCADE">
            <reference local="my_author_id" foreign="id" />
        </foreign-key>
    </table>

    <table name="my_author">
        <column name="id" type="integer" primaryKey="true" />
        <column name="name" type="varchar" size="255" />
    </table>
</database>
XML);
        }

        Propel::disableInstancePooling();

        $this->con = Propel::getConnection('value_resolver');
        $this->con->useDebug(true);

        MyBookQuery::create()->deleteAll($this->con);
        MyAuthorQuery::create()->deleteAll($this->con);

        $author = new MyAuthor();
        $author->setId(10);
        $author->setName('Will');

        $book = new MyBook();
        $book->setId(1);
        $book->setName('PropelBook');
        $book->setSlug('my-book');
        $book->setAuthorSlug('will');
        $book->setMyAuthor($author);

        $book2 = new MyBook();
        $book2->setId(2);
        $book2->setName('sf2lBook');
        $book2->setSlug('my-kewl-book');
        $book2->setAuthorSlug('my-author');
        $book2->setMyAuthor($author);

        $author->save($this->con);

        $this->resolver = new PropelValueResolver();
    }

    protected function tearDown(): void
    {
        $this->con->useDebug(false);

        Propel::enableInstancePooling();
    }

    public function testIgnoresNonModelArguments()
    {
        $request = new Request([], [], ['id' => 1]);

        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', null)));
        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', 'fakeClass')));
        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', TestCase::class)));
    }

    public function testIgnoresArgumentsWithoutMapModel()
    {
        $request = new Request([], [], ['id' => 1, 'book' => null]);

        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', MyBook::class)));
    }

    public function testFindPk()
    {
        $book = $this->resolveBook(['id' => 1, 'book' => null]);

        $this->assertSame('PropelBook', $book->getName());
    }

    public function testFindPkNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->resolveBook(['id' => 99, 'book' => null]);
    }

    public function testFindSlug()
    {
        $book = $this->resolveBook(['slug' => 'my-book', 'book' => null]);

        $this->assertSame(1, $book->getId());
    }

    public function testFindCamelCasedSlug()
    {
        $book = $this->resolveBook(['author_slug' => 'my-author', 'slug' => 'my-kewl-book', 'book' => null]);

        $this->assertSame(2, $book->getId());
    }

    public function testFindSlugNotFound()
    {
        $this->expectException(NotFoundHttpException::class);

        $this->resolveBook(['slug' => 'my-foo', 'book' => null]);
    }

    public function testFindBySlugNotByName()
    {
        $book = $this->resolveBook(['slug' => 'my-book', 'name' => 'foo', 'book' => null], new MapModel(exclude: ['name']));

        $this->assertSame(1, $book->getId());
    }

    public function testNothingToFindByWhenAllParametersAreExcluded()
    {
        $request = new Request([], [], ['slug' => 'my-book', 'name' => 'foo', 'book' => null]);

        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', MyBook::class, new MapModel(exclude: ['name', 'slug']))));
    }

    public function testNothingToFindByWhenIdIsExcluded()
    {
        $request = new Request([], [], ['id' => '1234', 'book' => null]);

        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', MyBook::class, new MapModel(exclude: ['id']))));
    }

    public function testNothingToFindBy()
    {
        $request = new Request([], [], ['book' => null]);

        $this->assertSame([], $this->resolver->resolve($request, $this->argument('book', MyBook::class, new MapModel())));
    }

    public function testNullableArgumentNotFound()
    {
        $request = new Request([], [], ['id' => 99, 'book' => null]);

        $this->assertSame([null], $this->resolver->resolve($request, $this->argument('book', MyBook::class, new MapModel(), true)));
    }

    public function testFindWithMapping()
    {
        $book = $this->resolveBook(['toto' => 1, 'book' => null], new MapModel(mapping: ['toto' => 'id']));

        $this->assertSame(1, $book->getId());
    }

    public function testFindSlugWithMapping()
    {
        $book = $this->resolveBook(['slugParam_special' => 'my-book', 'book' => null], new MapModel(mapping: ['slugParam_special' => 'slug']));

        $this->assertSame(1, $book->getId());
    }

    public function testWith()
    {
        $nb = $this->con->getQueryCount();
        $book = $this->resolveBook(['id' => 1, 'book' => null], new MapModel(with: 'MyAuthor'));

        $this->assertSame($nb + 1, $this->con->getQueryCount(), 'only one query to get the book');
        $this->assertInstanceOf(MyAuthor::class, $book->getMyAuthor());
        $this->assertSame($nb + 1, $this->con->getQueryCount(), 'no new query to get the author');
    }

    public function testWithLeftJoin()
    {
        $request = new Request([], [], ['param1' => 10, 'author' => null]);
        $argument = $this->argument('author', MyAuthor::class, new MapModel(mapping: ['param1' => 'id'], with: [['MyBook', 'left join']]));

        $nb = $this->con->getQueryCount();
        [$author] = $this->resolver->resolve($request, $argument);

        $this->assertInstanceOf(MyAuthor::class, $author);
        $this->assertSame($nb + 1, $this->con->getQueryCount(), 'only one query to get the author');

        $books = $author->getMyBooks();
        $this->assertInstanceOf(ObjectCollection::class, $books);
        $this->assertCount(2, $books, 'Author should have two books');
        $this->assertSame($nb + 1, $this->con->getQueryCount(), 'no new query to get the books');
    }

    public function testInvalidJoinType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->resolveBook(['id' => 1], new MapModel(with: [['MyAuthor', 'outer join']]));
    }

    public function testOptionsReadFromRequestAttributes()
    {
        $request = new Request([], [], [
            'toto' => 10,
            'author' => null,
            'propel_converter' => ['author' => ['mapping' => ['toto' => 'id']]],
        ]);

        [$author] = $this->resolver->resolve($request, $this->argument('author', MyAuthor::class));

        $this->assertInstanceOf(MyAuthor::class, $author);
        $this->assertSame(10, $author->getId());
    }

    private function resolveBook(array $attributes, ?MapModel $mapModel = null): MyBook
    {
        $result = $this->resolver->resolve(new Request([], [], $attributes), $this->argument('book', MyBook::class, $mapModel ?? new MapModel()));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(MyBook::class, $result[0]);

        return $result[0];
    }

    private function argument(string $name, ?string $type, ?MapModel $mapModel = null, bool $isNullable = false): ArgumentMetadata
    {
        return new ArgumentMetadata($name, $type, false, false, null, $isNullable, $mapModel ? [$mapModel] : []);
    }
}
