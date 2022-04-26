<?php

namespace EventEspresso\core\domain\services\admin\registrations;

use EE_Capabilities;
use EE_Datetime;
use EE_Error;
use EE_Event;
use EE_Registration;
use EEM_Event;
use ReflectionException;

class DatetimesForEventCheckIn
{
    /**
     * @var EE_Capabilities $caps
     */
    public $caps;

    /**
     * @var EE_Event
     */
    protected $event;

    /**
     * @var EE_Event[]
     */
    protected $all_events;

    /**
     * @var EE_Datetime[][]
     */
    protected $datetimes;

    /**
     * @var int
     */
    private $start_date_offset;


    /**
     * @param EE_Capabilities $capabilities
     * @param EE_Event|null   $event
     */
    public function __construct(EE_Capabilities $capabilities, ?EE_Event $event = null)
    {
        $this->event = $event;
        $this->caps = $capabilities;
        $this->start_date_offset = absint(
            apply_filters(
                'FHEE__EventEspresso_core_domain_services_admin_registrations_DatetimesForEventCheckIn__start_date_offset',
                HOUR_IN_SECONDS * 2,
                $this->event
            )
        );
    }


    /**
     * @param int $event_id
     * @return DatetimesForEventCheckIn
     * @throws EE_Error
     * @throws ReflectionException
     */
    public static function fromEventID(int $event_id): DatetimesForEventCheckIn
    {
        /** @var EE_Event $event */
        $event = EEM_Event::instance()->get_one_by_ID($event_id);
        return new DatetimesForEventCheckIn(EE_Capabilities::instance(), $event);
    }


    /**
     * @param EE_Event $event
     * @return DatetimesForEventCheckIn
     */
    public static function fromEvent(EE_Event $event): DatetimesForEventCheckIn
    {
        return new DatetimesForEventCheckIn(EE_Capabilities::instance(), $event);
    }


    /**
     * @param EE_Registration $registration
     * @return DatetimesForEventCheckIn
     * @throws EE_Error
     */
    public static function fromRegistration(EE_Registration $registration): DatetimesForEventCheckIn
    {
        return new DatetimesForEventCheckIn(EE_Capabilities::instance(), $registration->event());
    }


    /**
     * @return EE_Event
     */
    public function event(): EE_Event
    {
        return $this->event;
    }


    /**
     * @return array
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function getAllActiveDatetimesForAllEvents(): array
    {
        if ($this->all_events === null) {
            $where = [
                'Registration.REG_ID' => ['!=', null]
            ];
            if (! $this->caps->current_user_can('ee_read_private_events', 'get_events')) {
                $where['status**'] = ['!=', 'private'];
            }
            if (! $this->caps->current_user_can('ee_read_others_events', 'get_events')) {
                $where['EVT_wp_user'] = get_current_user_id();
            }
            $this->all_events = EEM_Event::instance()->get_all(
                [
                    $where,
                    'order_by' => ['Datetime.DTT_EVT_start' => 'DESC'],
                ]
            );
        }
        return $this->all_events;
    }


    /**
     * @return array
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function getAllActiveDatetimesForEvent(bool $active = true): array
    {
        $start_date = $active ? time() - $this->start_date_offset : null;
        return $this->event instanceof EE_Event
            ? $this->event->activeDatetimes($start_date)
            : [];
    }


    /**
     * @param int|null $DTD_ID If specific datetime ID is supplied, will return that date, but only if it is active.
     *                         If no ID is supplied but event only has one related datetime, then it will be returned.
     *                         If the above conditions are not met, then function will return null.
     * @param bool $active
     * @return EE_Datetime|null
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function getOneActiveDatetimeForEvent(?int $DTD_ID = 0, bool $active = true): ?EE_Datetime
    {
        $key = $active ? 1 : 0;
        if (! isset($this->datetimes[ $key ]) || $this->datetimes[ $key ] === null) {
            $this->datetimes[ $key ] = $this->getAllActiveDatetimesForEvent($active);
        }
        if ($DTD_ID) {
            foreach ($this->datetimes[ $key ] as $datetime) {
                if ($datetime instanceof EE_Datetime && $datetime->ID() === $DTD_ID) {
                    return $datetime;
                }
            }
            return null;
        }
        return count($this->datetimes[ $key ]) === 1 ? reset($this->datetimes[ $key ]) : null;
    }
}
