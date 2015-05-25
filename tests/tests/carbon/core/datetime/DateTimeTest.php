<?php

namespace tests\carbon\core\datetime;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\DateTimeUtils;
use carbon\core\datetime\zone\DateTimeZone;
use \DateTime as PHPDateTime;
use \DateTimeZone as PHPDateTimeZone;
use Exception;
use PHPUnit_Framework_TestCase;

/**
 * Class DateTimeTest
 *
 * @coversDefaultClass carbon\core\datetime\DateTime
 */
class DateTimeTest extends PHPUnit_Framework_TestCase {

    // TODO: Add tests for all methods!
    // TODO: Also test all exceptions!
    // TODO: Test invalid cases!

    /**
     * An array containing a few relative date and times for testing.
     *
     * @var array TEST_RELATIVE_DATE_TIMES Relative date and times as strings.
     */
    // TODO: Remove this!
    private static $TEST_RELATIVE_DATE_TIMES = Array(
        'now',
        'tomorrow',
        'next sunday',
        '+1 day +1 year'
    );
    /**
     * Array containing a few timezones for testing.
     *
     * @var array TEST_TIMEZONES Timezones as strings.
     */
    // TODO: Remove this!
    private static $TEST_TIMEZONES = Array(
        'Europe/Amsterdam',
        'Europe/Berlin',
        'America/New_York'
    );

    const OBJECT_DATETIME = 'carbon\\core\\datetime\\DateTime';
    // TODO: Move this to the DateTimeZone test class?
    const OBJECT_DATETIMEZONE = 'carbon\\core\\datetime\\zone\\DateTimeZone';

    /**
     * This set up method is called before each test case.
     * @covers ::__construct
     */
    public function setUp() {
        // Reset the mock now date and time
        DateTime::setMockNow(null);
    }

    // TODO: Sort all methods properly!
    // TODO: Is @covers ::__construct correct, or does this cover all constructors?

    /**
     * Test the constructor with a timestamp.
     *
     * @covers ::__construct
     */
    public function testConstructorTimestamp() {
        // Specify a timestamp and timezone
        $timestamp = time() + 60 * 60 * 24;
        $timezone = static::getNonDefaultTimezone();

        // Construct a DateTime object with this timestamp and timezone
        $dateTime = new DateTime($timestamp, $timezone);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with a timestamp and timezone, wrong object type');

        // Assert the timestamp and timezone
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to construct a DateTime object with a timestamp and timezone, wrong timestamp');
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to construct a DateTime object with a timestamp and timezone, wrong timezone');
    }

    /**
     * Test the constructor with a negative timestamp.
     *
     * @covers ::__construct
     *
     * @depends testConstructorTimestamp
     */
    public function testConstructorNegativeTimestamp() {
        // Specify a timestamp and timezone
        $timestamp = time() + 60 * 60 * 24 * -1;
        $timezone = static::getNonDefaultTimezone();

        // Construct a DateTime object with this timestamp and timezone
        $dateTime = new DateTime($timestamp, $timezone);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with a negative timestamp and timezone, wrong object type');

        // Assert the timestamp and timezone
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to construct a DateTime object with a negative timestamp and timezone, wrong timestamp');
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to construct a DateTime object with a negative timestamp and timezone, wrong timezone');
    }

    /**
     * Test the constructor with a DateTime object.
     *
     * @covers ::__construct
     *
     * @depends testConstructorTimestamp
     */
    public function testConstructorDateTime() {
        // Specify a timestamp, timezone and default timezone
        $timestamp = time() + 60 * 60 * 24;
        $timezone = static::getNonDefaultTimezone();
        $defaultTimezone = static::getDefaultTimezone();

        // Create a base DateTime instance with the specified timezone
        $baseDateTime = new DateTime($timestamp, $timezone);

        // Construct a DateTime instance with a DateTime instance and the default timezone
        $dateTime = new DateTime($baseDateTime, $defaultTimezone);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with another DateTime object, wrong object type');

        // Assert the timestamp and timezone
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to construct a DateTime object with another DateTime object, wrong timestamp');
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to construct a DateTime object with another DateTime object, wrong timezone');
    }

    /**
     * Test the constructor with a PHPDateTime object.
     *
     * @covers ::__construct
     *
     * @depends testConstructorTimestamp
     */
    // TODO: Implement timezones in this test!
    public function testConstructorPHPDateTime() {
        // Specify a timestamp and timezone
        $timestamp = time() + 60 * 60 * 24;
        $timezone = static::getNonDefaultTimezone();

        // Parse the timezone as PHPDateTimeZone
        $phpDateTimeZone = new PHPDateTimeZone($timezone);

        // Create a base PHPDateTime object
        $baseDateTime = new PHPDateTime('@' . $timestamp, $phpDateTimeZone);

        // Construct a DateTime object with a PHPDateTime instance
        $dateTime = new DateTime($baseDateTime);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with a PHPDateTime object, wrong object type');

        // Assert the timestamp
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to construct a DateTime object with a PHPDateTime object, wrong timestamp');
    }

    /**
     * Test the constructor with relative date and time strings.
     *
     * @covers ::__construct
     */
    // TODO: Implement timezones in this test!
    public function testConstructorRelativeTime() {
        // Specify a relative time
        $relative = 'tomorrow';

        // Try to construct a DateTime object with the relative time
        $dateTime = new DateTime($relative);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with a relative date and time string, wrong object type');

        // Create a base date and time object to compare the other object to
        $baseDateTime = new PHPDateTime($relative);

        // Assert the date and time
        $this->assertEquals($baseDateTime->format(DateTime::DEFAULT_FORMAT_DATE), $dateTime->format(DateTime::DEFAULT_FORMAT_DATE), 'Failed to construct a DateTime object with a relative date and time string, wrong date');
    }

    /**
     * Test the constructor with now/null time.
     *
     * @covers ::__construct
     *
     * @depends testConstructorPHPDateTime
     */
    // TODO: Depend the diffInSeconds() test
    public function testConstructorNow() {
        // Create a base date and time using PHPs DateTime object
        $baseDateTime = new DateTime(new PHPDateTime('now'));

        // Construct a DateTime object with the now time using null
        $dateTime = new DateTime(null);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with null, wrong object type');

        // Make sure the two date times don't differ much more than 2 seconds
        $this->lessThanOrEqual(2, $baseDateTime->diffInSeconds($dateTime, true), 'Failed to construct a DateTime object with null, time difference too big');

        // Construct a DateTime object with the now time using now
        $dateTime = new DateTime('now');
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to construct a DateTime object with null, wrong object type');

        // Make sure the two date times don't differ much more than 2 seconds
        $this->lessThanOrEqual(2, $baseDateTime->diffInSeconds($dateTime, true), 'Failed to construct a DateTime object with null, time difference too big');
    }

    /**
     * Test the create method using null parameters.
     *
     * @covers ::create
     */
    // TODO: Depend the diffInSeconds() and now() test
    // TODO: Add timezone check
    public function testCreateNull() {
        // Create a DateTime object with null parameters
        $dateTime = DateTime::create();
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object, wrong object type');

        // Make sure the date and time object doesn't differ more than 2 seconds with the current time
        $this->lessThanOrEqual(2, DateTime::now()->diffInSeconds($dateTime, true), 'Failed to create a DateTime object with null, time difference too big');
    }

    /**
     * Test the create method with a specified hour.
     *
     * @covers ::create
     */
    // TODO: Depend the now() test
    // TODO: Add timezone check
    public function testCreateHour() {
        // Create a DateTime object with a specified hour
        $dateTime = DateTime::create(null, null, null, 1);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object, wrong object type');

        // Assert the date and time
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object, wrong object type');
        $this->assertEquals(DateTime::now()->format(DateTime::DEFAULT_FORMAT_DATE), $dateTime->format(DateTime::DEFAULT_FORMAT_DATE), 'Failed to create a DateTime object, wrong date');
        $this->assertEquals(1, $dateTime->getHour(), 'Failed to create a DateTime object, wrong hour');
        $this->assertEquals(0, $dateTime->getMinute(), 'Failed to create a DateTime object, wrong minute');
        $this->assertEquals(0, $dateTime->getSecond(), 'Failed to create a DateTime object, wrong second');
    }

    /**
     * Test whether the create method works with specified date and time values.
     *
     * @covers ::create
     */
    // TODO: Depend the now() test
    // TODO: Add timezone check
    public function testCreateDatesTimes() {
        // Create a DateTime object with specified values
        $dateTime = DateTime::create(1, 2, 3, 4, 5, 6);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object, wrong object type');

        // Assert the date and time
        $this->assertEquals(1, $dateTime->getYear(), 'Failed to create a DateTime object, wrong year');
        $this->assertEquals(2, $dateTime->getMonth(), 'Failed to create a DateTime object, wrong month');
        $this->assertEquals(3, $dateTime->getDay(), 'Failed to create a DateTime object, wrong day');
        $this->assertEquals(4, $dateTime->getHour(), 'Failed to create a DateTime object, wrong hour');
        $this->assertEquals(5, $dateTime->getMinute(), 'Failed to create a DateTime object, wrong minute');
        $this->assertEquals(6, $dateTime->getSecond(), 'Failed to create a DateTime object, wrong second');
    }

    /**
     * Test the createFromDate method with null parameters.
     *
     * @covers ::createFromDate
     */
    // TODO: Depend the diffInSeconds() and now() test
    // TODO: Add timezone check
    public function testCreateFromDateNull() {
        // Create a DateTime object with null parameters
        $dateTime = DateTime::createFromDate();
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object with a date, wrong object type');

        // Make sure the date and time object doesn't differ more than 2 seconds with the current time
        $this->lessThanOrEqual(2, DateTime::now()->diffInSeconds($dateTime, true), 'Failed to create a DateTime object with null, time difference too big');
    }

    /**
     * Test the createFromDate method with specified date values.
     *
     * @covers ::createFromDate
     */
    // TODO: Add timezone check
    public function testCreateFromDateDates() {
        // Create a DateTime object with specified date values
        $dateTime = DateTime::createFromDate(1, 2, 3);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object with a date, wrong object type');

        // Assert the date values
        $this->assertEquals(1, $dateTime->getYear(), 'Failed to create a DateTime object with a date, wrong year');
        $this->assertEquals(2, $dateTime->getMonth(), 'Failed to create a DateTime object with a date, wrong month');
        $this->assertEquals(3, $dateTime->getDay(), 'Failed to create a DateTime object with a date, wrong day');
    }

    /**
     * Test the createFromTime method with null parameters.
     *
     * @covers ::createFromTime
     */
    // TODO: Depend the diffInSeconds() and now() test
    // TODO: Add a timezone checks!
    public function testCreateFromTimeNull() {
        // Create a DateTime object with null parameters
        $dateTime = DateTime::createFromTime();
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object with a time, wrong object type');

        // Make sure the date and time object doesn't differ more than 2 seconds with the current time
        $this->lessThanOrEqual(2, DateTime::now()->diffInSeconds($dateTime, true), 'Failed to create a DateTime object with null, time difference too big');
    }

    /**
     * Test the createFromTime method with a specified hour.
     *
     * @covers ::createFromTime
     */
    // TODO: Depend the now() test
    // TODO: Add a timezone checks!
    public function testCreateFromTimeHour() {
        // Create a DateTime object with a specified hour
        $dateTime = DateTime::createFromTime(1, null, null);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object with a time, wrong object type');

        // Assert the date and time
        $this->assertEquals(DateTime::now()->format(DateTime::DEFAULT_FORMAT_DATE), $dateTime->format(DateTime::DEFAULT_FORMAT_DATE), 'Failed to create a DateTime object with a time, wrong date');
        $this->assertEquals(1, $dateTime->getHour(), 'Failed to create a DateTime object with a time, wrong hour');
        $this->assertEquals(0, $dateTime->getMinute(), 'Failed to create a DateTime object with a time, wrong minute');
        $this->assertEquals(0, $dateTime->getSecond(), 'Failed to create a DateTime object with a time, wrong second');
    }

    /**
     * Test the createFromTime method with specified time values.
     *
     * @covers ::createFromTime
     */
    // TODO: Depend the now() test
    // TODO: Add a timezone checks!
    public function testCreateFromTimeTimes() {
        // Create a DateTime object with specified time values
        $dateTime = DateTime::createFromTime(1, 2, 3);
        $this->assertInstanceOf(static::OBJECT_DATETIME, $dateTime, 'Failed to create a DateTime object with a time, wrong object type');

        // Assert the date and time
        $this->assertEquals(DateTime::now()->format(DateTime::DEFAULT_FORMAT_DATE), $dateTime->format(DateTime::DEFAULT_FORMAT_DATE), 'Failed to create a DateTime object with a time, wrong date');
        $this->assertEquals(1, $dateTime->getHour(), 'Failed to create a DateTime object with a time, wrong hour');
        $this->assertEquals(2, $dateTime->getMinute(), 'Failed to create a DateTime object with a time, wrong minute');
        $this->assertEquals(3, $dateTime->getSecond(), 'Failed to create a DateTime object with a time, wrong second');
    }

    /**
     * Test whether the getYear method works.
     *
     * @covers ::getYear
     *
     * @depends testCreateFromDateDates
     */
    public function testGetYear() {
        // Create a DateTime instance with a specified year
        $year = 2000;
        $dateTime = DateTime::createFromDate($year, null, null);

        // Assert the year
        $this->assertEquals($year, $dateTime->getYear(), 'Failed to get the year of a DateTime instance');
    }

    /**
     * Test whether the setYear method works.
     *
     * @covers ::setYear
     *
     * @depends testGetYear
     */
    public function testSetYear() {
        // Create a DateTime object and set the year
        $year = 2000;
        $dateTime = DateTime::now()->setYear($year);

        // Assert the year
        $this->assertEquals($year, $dateTime->getYear(), 'Failed to set the year of a DateTime instance');
    }

    /**
     * Test whether the getQuarter method works.
     *
     * @covers ::getQuarter
     *
     * @depends testCreateFromDateDates
     */
    public function testGetQuarter() {
        // Create a DateTime object with the specified quarter
        $month = 7;
        $quarter = ceil($month / 3);
        $dateTime = DateTime::createFromDate(null, $month, null);

        // Assert the quarter
        $this->assertEquals($quarter, $dateTime->getQuarter(), 'Failed to get the quarter of a DateTime instance with the current time');
    }

    /**
     * Test whether the getMonth method works.
     *
     * @covers ::getMonth
     *
     * @depends testCreateFromDateDates
     */
    public function testGetMonth() {
        // Create a DateTime instance with a specified month
        $month = 6;
        $dateTime = DateTime::createFromDate(null, $month, null);

        // Assert the month
        $this->assertEquals($month, $dateTime->getMonth(), 'Failed to get the month of a DateTime instance');
    }

    /**
     * Test whether the setMonth method works.
     *
     * @covers ::setMonth
     *
     * @depends testGetMonth
     */
    public function testSetMonth() {
        // Create a DateTime object and set the month
        $month = 6;
        $dateTime = DateTime::now()->setMonth($month);

        // Assert the month
        $this->assertEquals($month, $dateTime->getMonth(), 'Failed to set the month of a DateTime instance');
    }

    /**
     * Test whether the getDay method works.
     *
     * @covers ::getDay
     *
     * @depends testCreateFromDateDates
     */
    public function testGetDay() {
        // Create a DateTime instance with a specified day
        $day = 15;
        $dateTime = DateTime::createFromDate(null, null, $day);

        // Assert the day
        $this->assertEquals($day, $dateTime->getDay(), 'Failed to get the day of a DateTime instance');
    }

    /**
     * Test whether the setDay method works.
     *
     * @covers ::setDay
     *
     * @depends testGetDay
     */
    public function testSetDay() {
        // Create a DateTime object and set the day
        $day = 15;
        $dateTime = DateTime::now()->setDay($day);

        // Assert the day
        $this->assertEquals($day, $dateTime->getDay(), 'Failed to set the day of a DateTime instance');
    }

    /**
     * Test whether the getHour method works.
     *
     * @covers ::getHour
     *
     * @depends testCreateFromTimeTimes
     */
    public function testGetHour() {
        // Create a DateTime instance with a specified hour
        $hour = 12;
        $dateTime = DateTime::createFromTime($hour, null, null);

        // Assert the hour
        $this->assertEquals($hour, $dateTime->getHour(), 'Failed to get the hour of a DateTime instance');
    }

    /**
     * Test whether the setHour method works.
     *
     * @covers ::setHour
     *
     * @depends testGetHour
     */
    public function testSetHour() {
        // Create a DateTime object and set the hour
        $hour = 12;
        $dateTime = DateTime::now()->setHour($hour);

        // Assert the hour
        $this->assertEquals($hour, $dateTime->getHour(), 'Failed to set the hour of a DateTime instance');
    }

    /**
     * Test whether the getMinute method works.
     *
     * @covers ::getMinute
     *
     * @depends testCreateFromTimeTimes
     */
    public function testGetMinute() {
        // Create a DateTime instance with a specified minute
        $minute = 30;
        $dateTime = DateTime::createFromTime(null, $minute, null);

        // Assert the minute
        $this->assertEquals($minute, $dateTime->getMinute(), 'Failed to get the minute of a DateTime instance');
    }

    /**
     * Test whether the setMinute method works.
     *
     * @covers ::setMinute
     *
     * @depends testGetMinute
     */
    public function testSetMinute() {
        // Create a DateTime object and set the minute
        $minute = 30;
        $dateTime = DateTime::now()->setMinute($minute);

        // Assert the minute
        $this->assertEquals($minute, $dateTime->getMinute(), 'Failed to set the minute of a DateTime instance');
    }

    /**
     * Test whether the getSecond method works.
     *
     * @covers ::getSecond
     *
     * @depends testCreateFromTimeTimes
     */
    public function testGetSecond() {
        // Create a DateTime instance with a specified second
        $second = 30;
        $dateTime = DateTime::createFromTime(null, null, $second);

        // Assert the second
        $this->assertEquals($second, $dateTime->getSecond(), 'Failed to get the second of a DateTime instance');
    }

    /**
     * Test whether the setSecond method works.
     *
     * @covers ::setSecond
     *
     * @depends testGetSecond
     */
    public function testSetSecond() {
        // Create a DateTime object and set the second
        $second = 30;
        $dateTime = DateTime::now()->setSecond($second);

        // Assert the second
        $this->assertEquals($second, $dateTime->getSecond(), 'Failed to set the second of a DateTime instance');
    }

    /**
     * Test whether the setTimestamp method works with a timestamp.
     *
     * @covers ::setTimestamp
     */
    public function testSetTimestampPositive() {
        // Specify a timestamp
        $timestamp = 1234567890;

        // Create a base DateTime object and set the timestamp
        $dateTime = DateTime::now();
        $dateTime->setTimestamp($timestamp);

        // Assert the timestamp
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to set the timestamp of a DateTime instance');
    }

    /**
     * Test whether the setTimestamp method works with a negative timestamp.
     *
     * @covers ::setTimestamp
     *
     * @depends testSetTimestampPositive
     */
    public function testSetTimestampNegative() {
        // Specify a negative timestamp
        $timestamp = -1234567890;

        // Create a base DateTime object and set the timestamp
        $dateTime = DateTime::now();
        $dateTime->setTimestamp($timestamp);

        // Assert the timestamp
        $this->assertEquals($timestamp, $dateTime->getTimestamp(), 'Failed to set a negative timestamp of a DateTime instance');
    }

    /**
     * Test whether the setTimestamp method works with a null parameter.
     *
     * @covers ::setTimestamp
     *
     * @depends testSetTimestampPositive
     */
    public function testSetTimestampNull() {
        // Create a DateTime object and set the timestamp to null
        $dateTime = DateTime::create(2000, 1, 2, 3, 4, 5);
        $dateTime->setTimestamp(null);

        // Make sure the date and time object doesn't differ more than 2 seconds with the current time
        $this->lessThanOrEqual(2, DateTime::now()->diffInSeconds($dateTime, true), 'Failed to set the timestamp of a DateTime object with null, time difference too big');
    }

    /**
     * Test whether the getTimezone method works with a default timezone.
     *
     * @covers ::getTimezone
     */
    public function testGetTimezoneDefault() {
        // Specify and get a timezone
        $timezone = self::getDefaultTimezone();

        // Create a base DateTime object with the specified timezone
        $baseDateTime = DateTime::now($timezone);

        // Get the timezone from the base object and make sure it's instance is valid
        $dateTimeTimezone = $baseDateTime->getTimezone();
        $this->assertInstanceOf(self::OBJECT_DATETIMEZONE, $dateTimeTimezone, 'Failed to get the timezone of a DateTime instance, because the returned timezone object isn\'t a valid instance');

        // Compare the timezone to the original specified timezone
        $dateTimeTimezone = $dateTimeTimezone->getName();
        $this->assertEquals($timezone, $dateTimeTimezone, 'Failed to get the timezone of a DateTime instance, the timezone names are different');
    }

    /**
     * Test whether the getTimezone method works with a non-default timezone.
     *
     * @covers ::getTimezone
     */
    public function testGetTimezoneNonDefault() {
        // Specify and get a timezone
        $timezone = self::getNonDefaultTimezone();

        // Create a base DateTime object with the specified timezone
        $baseDateTime = DateTime::now($timezone);

        // Get the timezone from the base object and make sure it's instance is valid
        $dateTimeTimezone = $baseDateTime->getTimezone();
        $this->assertInstanceOf(self::OBJECT_DATETIMEZONE, $dateTimeTimezone, 'Failed to get the timezone of a DateTime instance, because the returned timezone object isn\'t a valid instance');

        // Compare the timezone to the original specified timezone
        $dateTimeTimezone = $dateTimeTimezone->getName();
        $this->assertEquals($timezone, $dateTimeTimezone, 'Failed to get the timezone of a DateTime instance, the timezone names are different');
    }

    /**
     * Test whether the setTimezone method works with a DateTimeZone object.
     *
     * @covers ::setTimezone
     *
     * @depends testGetTimezoneNonDefault
     */
    // TODO: Depend on the DateTimeZone::parse method?
    public function testSetTimezoneDateTimeZone() {
        // Specify the timezone, and create a DateTimeZone object
        $timezoneId = self::getNonDefaultTimezone();
        $timezone = DateTimeZone::parse($timezoneId);
        $this->assertInstanceOf(self::OBJECT_DATETIMEZONE, $timezone, 'Failed to parse a DateTimeZone instance used for testing');

        // Create a DateTime object and set the timezone
        $dateTime = DateTime::now();
        $dateTime->setTimezone($timezone);

        // Assert the timezones
        $this->assertEquals($timezoneId, $dateTime->getTimezone()->getName(), 'Failed to set the timezone with a DateTimeZone instance of a DateTime instance');
    }

    /**
     * Test whether the setTimezone method works with a PHPDateTimeZone object.
     *
     * @covers ::setTimezone
     *
     * @depends testSetTimezoneDateTimeZone
     */
    public function testSetTimezonePHPDateTimeZone() {
        // Specify the timezone, and create a DateTimeZone object
        $timezoneId = self::getNonDefaultTimezone();
        $timezone = new PHPDateTimeZone($timezoneId);
        $this->assertInstanceOf('DateTimeZone', $timezone, 'Failed to parse a DateTimeZone instance used for testing');

        // Create a DateTime object and set the timezone
        $dateTime = DateTime::now();
        $dateTime->setTimezone($timezone);

        // Assert the timezones
        $this->assertEquals($timezoneId, $dateTime->getTimezone()->getName(), 'Failed to set the timezone with a PHPDateTimeZone instance');
    }

    /**
     * Test whether the setTimezone method works with a timezone identifier as a string.
     *
     * @covers ::setTimezone
     *
     * @depends testGetTimezoneNonDefault
     */
    public function testSetTimezoneTimezoneIdentifier() {
        // Specify the timezone
        $timezone = self::getNonDefaultTimezone();

        // Create a DateTime object and set the timezone
        $dateTime = DateTime::now();
        $dateTime->setTimezone($timezone);

        // Assert the timezones
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to set the timezone with a timezone identifier as a string');
    }

    /**
     * Test whether the setTimezone method works with a DateTime object.
     *
     * @covers ::setTimezone
     *
     * @depends testConstructorNow
     * @depends testGetTimezoneNonDefault
     */
    public function testSetTimezoneDateTime() {
        // Specify the timezone
        $timezone = self::getNonDefaultTimezone();

        // Create a DateTime object with the specified timezone
        $baseDateTime = new DateTime(null, $timezone);
        $this->assertInstanceOf(self::OBJECT_DATETIME, $baseDateTime, 'Failed to create a DateTime object, used for testing');

        // Create a DateTime object and set the timezone with the base object
        $dateTime = DateTime::now();
        $dateTime->setTimezone($baseDateTime);

        // Assert the timezones
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to set the timezone with a DateTime instance');
    }

    /**
     * Test whether the setTimezone method works with a PHPDateTime object.
     *
     * @covers ::setTimezone
     *
     * @depends testGetTimezoneNonDefault
     */
    public function testSetTimezonePHPDateTime() {
        // Specify the timezone
        $timezone = self::getNonDefaultTimezone();

        // Create a DateTime object with the specified timezone
        $baseDateTime = new PHPDateTime('now', new PHPDateTimeZone($timezone));
        $this->assertInstanceOf('DateTime', $baseDateTime, 'Failed to create a PHPDateTime object, used for testing');

        // Create a DateTime object and set the timezone with the base object
        $dateTime = DateTime::now();
        $dateTime->setTimezone($baseDateTime);

        // Assert the timezones
        $this->assertEquals($timezone, $dateTime->getTimezone()->getName(), 'Failed to set the timezone with a PHPDateTime instance');
    }

    /**
     * Test whether the setTimezone method works with a null property to set the timezone to the default.
     *
     * @covers ::setTimezone
     *
     * @depends testGetTimezoneNonDefault
     */
    // TODO: Depends now().
    public function testSetTimezoneDefault() {
        // Specify the timezone
        $nonDefaultTimezone = self::getNonDefaultTimezone();
        $defaultTimezone = self::getDefaultTimezone();

        // Create a DateTime object with the non default timezone, and set it to the default using null
        $dateTime = DateTime::now($nonDefaultTimezone);
        $dateTime->setTimezone(null);

        // Assert the timezone
        $this->assertEquals($defaultTimezone, $dateTime->getTimezone()->getName(), 'Failed to set the default timezone using null');
    }

    /**
     * Test whether the getOffset method works with the UTC timezone.
     *
     * @covers ::getOffset
     */
    // TODO: Depends now().
    public function testGetOffsetUTC() {
        // Specify the timezone to use
        $timezone = 'UTC';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in seconds
        $this->assertEquals(0, $dateTime->getOffset(), 'Failed to get the offset of a UTC DateTime object');
    }

    /**
     * Test whether the getOffset method works with a timezone that has a variable offset.
     *
     * @covers ::getOffset
     */
    // TODO: Depends now().
    public function testGetOffsetVariable() {
        // Specify the timezone to use
        $timezone = 'Europe/Amsterdam';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in seconds
        $this->assertGreaterThanOrEqual(1 * 60 * 60, $dateTime->getOffset(), 'Failed to get the proper offset of a DateTime object');
        $this->assertLessThanOrEqual(2 * 60 * 60, $dateTime->getOffset(), 'Failed to get the proper offset of a DateTime object');
    }

    /**
     * Test whether the getOffset method works with a timezone that has a fixed offset.
     *
     * @covers ::getOffset
     */
    // TODO: Depends now().
    public function testGetOffsetFixed() {
        // Specify the timezone to use
        $timezone = 'Pacific/Honolulu';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in seconds
        $this->assertEquals(-10 * 60 * 60, $dateTime->getOffset(), 'Failed to get the proper offset of a DateTime object');
    }

    /**
     * Test whether the getOffset method works with the UTC timezone.
     *
     * @covers ::getOffsetHours
     */
    // TODO: Depends now().
    public function testGetOffsetHoursUTC() {
        // Specify the timezone to use
        $timezone = 'UTC';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in hours
        $this->assertEquals(0, $dateTime->getOffsetHours(), 'Failed to get the offset in hours of a UTC DateTime object');
    }

    /**
     * Test whether the getOffset method works with a timezone that has a variable offset.
     *
     * @covers ::getOffsetHours
     */
    // TODO: Depends now().
    public function testGetOffsetHoursVariable() {
        // Specify the timezone to use
        $timezone = 'Europe/Amsterdam';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in hours
        $this->assertGreaterThanOrEqual(1, $dateTime->getOffsetHours(), 'Failed to get the proper offset in hours of a DateTime object');
        $this->assertLessThanOrEqual(2, $dateTime->getOffsetHours(), 'Failed to get the proper offset in hours of a DateTime object');
    }

    /**
     * Test whether the getOffset method works with a timezone that has a fixed offset.
     *
     * @covers ::getOffsetHours
     */
    // TODO: Depends now().
    public function testGetOffsetHoursFixed() {
        // Specify the timezone to use
        $timezone = 'Pacific/Honolulu';

        // Create a DateTime object with the specified timezone
        $dateTime = DateTime::now($timezone);

        // Assert the offset in hours
        $this->assertEquals(-10, $dateTime->getOffsetHours(), 'Failed to get the proper offset in hours of a DateTime object');
    }

    /**
     * Test whether the getMockNow method works with the default value.
     *
     * @covers ::getMockNow
     */
    public function testGetMockNowDefault() {
        // The method should return null by default
        $this->assertNull(DateTime::getMockNow(), 'Failed to get the mock date and time');
    }

    /**
     * Test whether the getMockNow method works when a mock date and time is set.
     *
     * @covers ::getMockNow
     */
    // TODO: Depends on setMockNow and parse?
    // TODO: Move this to setMockNow?
    public function testGetMockNowDateTime() {
        // Set the mock date and time with a DateTime instance
        $mockDateTime = DateTime::parse('-1 day');
        DateTime::setMockNow($mockDateTime);

        // Get and test the mock date and time
        $mock = DateTime::getMockNow();
        $this->assertInstanceOf(self::OBJECT_DATETIME, $mock, 'Failed to get the mock date and time instance, an invalid object is returned');
        $this->assertTrue($mockDateTime->equals($mock), 'The mock date and time is different than specified');
    }




    // TODO: Update the methods below

    /**
     * Test whether the parse method works.
     *
     * @covers ::parse
     */
    // TODO: Make sure this method doesn't fail if the date/time changed because of slow code.
    public function testParse() {
        // Test parsing a DateTime instance
        $dateTime = DateTime::now();
        $parsed = DateTime::parse($dateTime);
        $this->assertEquals($dateTime, $parsed, 'Failed to parse a DateTime instance');

        // Test parsing a different DateTime instance
        $dateTime = DateTime::createTomorrow();
        $parsed = DateTime::parse($dateTime);
        $this->assertEquals($dateTime, $parsed, 'Failed to parse a DateTime instance');

        // Make sure the timezone parameter is ignored when parsing DateTime objects
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $dateTime = DateTime::createTomorrow($timezone);
            $parsed = DateTime::parse($dateTime->copy(), 'Europe/Amsterdam');
            $this->assertEquals($dateTime, $parsed, 'The preferred timezone shouldn\'t be used when parsing DateTime objects');
        }

        // Make sure the timezones remains when parsing DateTime objects
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $dateTime = new DateTime('tomorrow', $timezone);
            $parsed = DateTime::parse($dateTime->copy());
            $this->assertEquals($dateTime, $parsed, 'Failed to parse a DateTime object without loosing the timezone');
        }

        // Test parsing PHPs DateTime object
        $phpDateTime = DateTime::parse(new PHPDateTime('tomorrow'));
        $dateTime = new DateTime('tomorrow');
        $this->assertEquals($dateTime, $phpDateTime, 'Failed to parse PHPs DateTime object');

        // Make sure the timezone parameter is ignored when parsing PHPs DateTime objects
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $phpDateTime = new PHPDateTime('tomorrow', new PHPDateTimeZone($timezone));
            $expected = DateTime::parse($phpDateTime);
            $parsed = DateTime::parse($phpDateTime, 'Europe/Amsterdam');
            $this->assertEquals($expected, $parsed,
                'The preferred timezone shouldn\'t be used when parsing PHPs DateTime objects');
        }

        // Make sure the timezones remains when parsing PHPs DateTime objects
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $dateTime = new PHPDateTime('tomorrow', new PHPDateTimeZone($timezone));
            $parsed = DateTime::parse($dateTime);
            $expected = DateTime::parse($dateTime);
            $this->assertEquals($expected, $parsed, 'Failed to parse PHPs DateTime object because of a timezone difference');
        }

        // Test a normal timestamp
        $timestamp = time();
        $dateTimeTimestamp = DateTime::parse($timestamp)->getTimestamp();
        $this->assertEquals($timestamp, $dateTimeTimestamp, 'Failed to parse a normal timestamp');

        // Test a negative timestamp
        $timestamp = -time();
        $dateTimeTimestamp = DateTime::parse($timestamp)->getTimestamp();
        $this->assertEquals($timestamp, $dateTimeTimestamp, 'Failed to parse a negative timestamp');

        // Make sure the timezones are parsed correctly with a timestamp
        $timestamp = time();
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $dateTime = new DateTime($timestamp, $timezone);
            $parsed = DateTime::parse($timestamp, $timezone);
            $this->assertEquals($dateTime, $parsed, 'Failed to parse a timestamp with a timezone');
        }

        // Test each relative date and time
        foreach(static::$TEST_RELATIVE_DATE_TIMES as $relativeDateTime) {
            // Test parsing PHPs DateTime object
            $expected = DateTime::parse(new PHPDateTime($relativeDateTime));
            $parsed = DateTime::parse($relativeDateTime);
            $this->assertEquals($expected, $parsed, 'Failed to parse a relative date and time of \'' . $relativeDateTime . '\'');
        }

        // Make sure the timezones are parsed correctly with a relative time
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $dateTime = new DateTime('tomorrow', $timezone);
            $parsed = DateTime::parse('tomorrow', $timezone);
            $this->assertEquals($dateTime, $parsed, 'Failed to parse a relative date and time with a timezone');
        }

        // Test parsing PHPs DateTime object
        $parsed = DateTime::parse(null);
        $this->assertInstanceOf('carbon\core\datetime\DateTime', $parsed, 'Failed to parse null as now');

        // Make sure the timezones are parsed correctly with a timestamp
        foreach(static::$TEST_TIMEZONES as $timezone) {
            $parsed = DateTime::parse(null, $timezone)->getTimezone()->getName();
            $this->assertEquals($timezone, $parsed, 'Failed to parse a null as now with a timezone');
        }
    }

    // TODO: Test method for __get, __isset, __set here!


    /**
     * Test whether the hasMockNow method works.
     *
     * @covers ::hasMockNow
     */
    public function testHasMockNow() {
        // Make sure there isn't any mock date and time
        $this->assertFalse(DateTime::hasMockNow(), 'Failed to check whether there\'s any mock date and time set');

        // Set the mock date and time
        DateTime::setMockNow(DateTime::now());

        // Make sure there's a mock date and time set
        $this->assertTrue(DateTime::hasMockNow(), 'Failed to check whether there\'s any mock date and time set');
    }

    /**
     * Test whether the setMockNow method works.
     *
     * @covers ::setMockNow
     */
    public function testSetMockNow() {
        // The method should return null by default
        $this->assertNull(DateTime::getMockNow(), 'Failed to get the mock date and time');

        // Set the mock date and time with a DateTime instance
        $mockDateTime = DateTime::parse('-1 day');
        DateTime::setMockNow($mockDateTime);

        // Get and test the mock date and time
        $mock = DateTime::getMockNow();
        $this->assertInstanceOf(self::OBJECT_DATETIME, $mock, 'Failed to get the mock date and time instance, an invalid object is returned');
        $this->assertTrue($mockDateTime->equals($mock), 'The mock date and time is different than specified');

        // Set the mock date and time with a relative date and time string
        $relativeDateTime = '+1 day';
        $mockDateTime = DateTime::parse($relativeDateTime);
        DateTime::setMockNow($relativeDateTime);

        // Get and test the mock date and time
        $mock = DateTime::getMockNow();
        $this->assertInstanceOf(self::OBJECT_DATETIME, $mock, 'Failed to get the mock date and time instance, an invalid object is returned');
        $this->assertTrue($mockDateTime->equals($mock), 'The mock date and time is different than specified');
    }

    /**
     * Get the default date and timezone as a string.
     *
     * @return string Default timezone.
     */
    public static function getDefaultTimezone() {
        return date_default_timezone_get();
    }

    /**
     * Get a date and timezone that isn't the default as a string.
     *
     * @return string Non-default timezone.
     *
     * @throws Exception Throws Exception if no non-default timezone could be found.
     */
    public static function getNonDefaultTimezone() {
        // Get and return a timezone that isn't the default
        foreach(timezone_identifiers_list() as $timezone)
            if($timezone != static::getDefaultTimezone())
                return $timezone;

        // No timezone found, throw an exception
        throw new Exception('Failed to get a non-default timezone');
    }
}