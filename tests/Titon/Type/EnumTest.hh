<?hh
namespace Titon\Type;

use Titon\Test\TestCase;

class EnumTest extends TestCase {

    protected $warrior;
    protected $ranger;
    protected $mage;

    protected function setUp() {
        parent::setUp();

        $this->warrior = new EnumStub(EnumStub::WARRIOR);
        $this->ranger = new EnumStub(EnumStub::RANGER);
        $this->mage = new EnumStub(EnumStub::MAGE);
    }

    /**
     * @expectedException \Titon\Type\Exception\InvalidEnumerableException
     */
    public function testConstructorErrorNoEnum() {
        new EnumStub(5);
    }

    public function testConstructorArgs() {
        $this->assertEquals('Warrior', $this->warrior->name);
        $this->assertEquals('Ranger', $this->ranger->name);
        $this->assertEquals('Mage', $this->mage->name);

        $this->assertEquals(true, $this->warrior->melee);
        $this->assertEquals(false, $this->ranger->melee);
        $this->assertEquals(false, $this->mage->melee);

        $this->assertEquals(1000, $this->warrior->health);
        $this->assertEquals(500, $this->ranger->health);
        $this->assertEquals(300, $this->mage->health);

        $this->assertEquals(0, $this->warrior->energy);
        $this->assertEquals(250, $this->ranger->energy);
        $this->assertEquals(600, $this->mage->energy);
    }

    public function testConstructorStatic() {
        $warrior = EnumStub::WARRIOR();

        $this->assertTrue($warrior->is(EnumStub::WARRIOR));
    }

    /**
     * @expectedException \Titon\Type\Exception\InvalidEnumerableException
     */
    public function testConstructorStaticError() {
        EnumStub::WARLOCK();
    }

    public function testToString() {
        $this->assertEquals('WARRIOR', (string) $this->warrior);
        $this->assertEquals('RANGER', $this->ranger->name());
        $this->assertEquals('MAGE', (string) $this->mage);
    }

    public function testIs() {
        $this->assertTrue($this->warrior->is(EnumStub::WARRIOR));
        $this->assertFalse($this->ranger->is(EnumStub::WARRIOR));
        $this->assertFalse($this->mage->is(EnumStub::WARRIOR));

        $this->assertFalse($this->warrior->is(EnumStub::RANGER));
        $this->assertTrue($this->ranger->is(EnumStub::RANGER));
        $this->assertFalse($this->mage->is(EnumStub::RANGER));

        $this->assertFalse($this->warrior->is(EnumStub::MAGE));
        $this->assertFalse($this->ranger->is(EnumStub::MAGE));
        $this->assertTrue($this->mage->is(EnumStub::MAGE));
    }

    public function testValue() {
        $this->assertEquals(EnumStub::WARRIOR, $this->warrior->value());
        $this->assertEquals(EnumStub::RANGER, $this->ranger->value());
        $this->assertEquals(EnumStub::MAGE, $this->mage->value());
    }

    public function testValues() {
        $this->assertEquals(Map {
            'WARRIOR' => 0,
            'RANGER' => 1,
            'MAGE' => 2
        }, EnumStub::values());
    }

    public function testKeys() {
        $this->assertEquals(Vector {'WARRIOR', 'RANGER', 'MAGE'}, EnumStub::keys());
    }

}

class EnumStub extends Enum {

    const int WARRIOR = 0;
    const int RANGER = 1;
    const int MAGE = 2;

    public string $name = '';
    public bool $melee = false;
    public int $health = 0;
    public int $energy = 0;

    protected Map<int, Vector<mixed>> $_enums = Map {
        self::WARRIOR => Vector {'Warrior', true, 1000},
        self::RANGER => Vector {'Ranger', false, 500, 250},
        self::MAGE => Vector {'Mage', false, 300, 600}
    };

    public function initialize(string $name, bool $melee, int $health, int $energy = 0): void {
        $this->name = $name;
        $this->melee = $melee;
        $this->health = $health;
        $this->energy = $energy;
    }

}