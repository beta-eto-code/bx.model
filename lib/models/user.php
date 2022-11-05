<?php

namespace Bx\Model\Models;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Bx\Model\AbsOptimizedModel;

class User extends AbsOptimizedModel
{
    protected function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'second_name' => $this->getSecondName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone()
        ];
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return (int)$this['ID'];
    }

    /**
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this["ID"] = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this['NAME'];
    }

    /**
     * @param string $value
     * @return void
     */
    public function setName(string $value)
    {
        $this["NAME"] = $value;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return (string)$this['LAST_NAME'];
    }

    /**
     * @param string $value
     * @return void
     */
    public function setLastName(string $value)
    {
        $this["LAST_NAME"] = $value;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return (string)$this['SECOND_NAME'];
    }

    /**
     * @param string $value
     * @return void
     */
    public function setSecondName(string $value)
    {
        $this["SECOND_NAME"] = $value;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return (string)$this["EMAIL"];
    }

    /**
     * @param string $value
     * @return void
     */
    public function setEmail(string $value)
    {
        $this["EMAIL"] = $value;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return (string)$this['PERSONAL_PHONE'];
    }


    /**
     * @return ?DateTime
     */
    public function getTimestampX(): ?DateTime
    {
        return $this["TIMESTAMP_X"] instanceof DateTime ? $this["TIMESTAMP_X"] : null;
    }


    /**
     * @param DateTime $value
     * @return void
     */
    public function setTimestampX(DateTime $value)
    {
        $this["TIMESTAMP_X"] = $value;
    }


    /**
     * @return string
     */
    public function getLogin(): string
    {
        return (string)$this["LOGIN"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setLogin(string $value)
    {
        $this["LOGIN"] = $value;
    }

    /**
     * @return string
     */
    public function getCheckword(): string
    {
        return (string)$this["CHECKWORD"];
    }


    /**
     * @return string
     */
    public function getActive(): string
    {
        return (string)$this["ACTIVE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setActive(string $value)
    {
        $this["ACTIVE"] = $value;
    }


    /**
     * @return ?DateTime
     */
    public function getLastLogin(): ?DateTime
    {
        return $this["LAST_LOGIN"] instanceof DateTime ? $this["LAST_LOGIN"] : null;
    }


    /**
     * @param DateTime $value
     * @return void
     */
    public function setLastLogin(DateTime $value)
    {
        $this["LAST_LOGIN"] = $value;
    }


    /**
     * @return ?DateTime
     */
    public function getDateRegister(): ?DateTime
    {
        return $this["DATE_REGISTER"] instanceof DateTime ? $this["DATE_REGISTER"] : null;
    }


    /**
     * @param DateTime $value
     * @return void
     */
    public function setDateRegister(DateTime $value)
    {
        $this["DATE_REGISTER"] = $value;
    }


    /**
     * @return string
     */
    public function getLid(): string
    {
        return (string)$this["LID"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setLid(string $value)
    {
        $this["LID"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalProfession(): string
    {
        return (string)$this["PERSONAL_PROFESSION"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalProfession(string $value)
    {
        $this["PERSONAL_PROFESSION"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalWww(): string
    {
        return (string)$this["PERSONAL_WWW"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalWww(string $value)
    {
        $this["PERSONAL_WWW"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalIcq(): string
    {
        return (string)$this["PERSONAL_ICQ"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalIcq(string $value)
    {
        $this["PERSONAL_ICQ"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalGender(): string
    {
        return (string)$this["PERSONAL_GENDER"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalGender(string $value)
    {
        $this["PERSONAL_GENDER"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalBirthdate(): string
    {
        return (string)$this["PERSONAL_BIRTHDATE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalBirthdate(string $value)
    {
        $this["PERSONAL_BIRTHDATE"] = $value;
    }


    /**
     * @return int
     */
    public function getPersonalPhoto(): int
    {
        return (int)$this["PERSONAL_PHOTO"];
    }


    /**
     * @param int $value
     * @return void
     */
    public function setPersonalPhoto(int $value)
    {
        $this["PERSONAL_PHOTO"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalPhone(): string
    {
        return (string)$this["PERSONAL_PHONE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalPhone(string $value)
    {
        $this["PERSONAL_PHONE"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalFax(): string
    {
        return (string)$this["PERSONAL_FAX"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalFax(string $value)
    {
        $this["PERSONAL_FAX"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalMobile(): string
    {
        return (string)$this["PERSONAL_MOBILE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalMobile(string $value)
    {
        $this["PERSONAL_MOBILE"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalPager(): string
    {
        return (string)$this["PERSONAL_PAGER"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalPager(string $value)
    {
        $this["PERSONAL_PAGER"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalStreet(): string
    {
        return (string)$this["PERSONAL_STREET"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalStreet(string $value)
    {
        $this["PERSONAL_STREET"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalMailbox(): string
    {
        return (string)$this["PERSONAL_MAILBOX"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalMailbox(string $value)
    {
        $this["PERSONAL_MAILBOX"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalCity(): string
    {
        return (string)$this["PERSONAL_CITY"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalCity(string $value)
    {
        $this["PERSONAL_CITY"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalState(): string
    {
        return (string)$this["PERSONAL_STATE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalState(string $value)
    {
        $this["PERSONAL_STATE"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalZip(): string
    {
        return (string)$this["PERSONAL_ZIP"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalZip(string $value)
    {
        $this["PERSONAL_ZIP"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalCountry(): string
    {
        return (string)$this["PERSONAL_COUNTRY"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalCountry(string $value)
    {
        $this["PERSONAL_COUNTRY"] = $value;
    }


    /**
     * @return string
     */
    public function getPersonalNotes(): string
    {
        return (string)$this["PERSONAL_NOTES"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setPersonalNotes(string $value)
    {
        $this["PERSONAL_NOTES"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkCompany(): string
    {
        return (string)$this["WORK_COMPANY"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkCompany(string $value)
    {
        $this["WORK_COMPANY"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkDepartment(): string
    {
        return (string)$this["WORK_DEPARTMENT"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkDepartment(string $value)
    {
        $this["WORK_DEPARTMENT"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkPosition(): string
    {
        return (string)$this["WORK_POSITION"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkPosition(string $value)
    {
        $this["WORK_POSITION"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkWww(): string
    {
        return (string)$this["WORK_WWW"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkWww(string $value)
    {
        $this["WORK_WWW"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkPhone(): string
    {
        return (string)$this["WORK_PHONE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkPhone(string $value)
    {
        $this["WORK_PHONE"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkFax(): string
    {
        return (string)$this["WORK_FAX"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkFax(string $value)
    {
        $this["WORK_FAX"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkPager(): string
    {
        return (string)$this["WORK_PAGER"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkPager(string $value)
    {
        $this["WORK_PAGER"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkStreet(): string
    {
        return (string)$this["WORK_STREET"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkStreet(string $value)
    {
        $this["WORK_STREET"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkMailbox(): string
    {
        return (string)$this["WORK_MAILBOX"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkMailbox(string $value)
    {
        $this["WORK_MAILBOX"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkCity(): string
    {
        return (string)$this["WORK_CITY"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkCity(string $value)
    {
        $this["WORK_CITY"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkState(): string
    {
        return (string)$this["WORK_STATE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkState(string $value)
    {
        $this["WORK_STATE"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkZip(): string
    {
        return (string)$this["WORK_ZIP"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkZip(string $value)
    {
        $this["WORK_ZIP"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkCountry(): string
    {
        return (string)$this["WORK_COUNTRY"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkCountry(string $value)
    {
        $this["WORK_COUNTRY"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkProfile(): string
    {
        return (string)$this["WORK_PROFILE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkProfile(string $value)
    {
        $this["WORK_PROFILE"] = $value;
    }


    /**
     * @return int
     */
    public function getWorkLogo(): int
    {
        return (int)$this["WORK_LOGO"];
    }


    /**
     * @param int $value
     * @return void
     */
    public function setWorkLogo(int $value)
    {
        $this["WORK_LOGO"] = $value;
    }


    /**
     * @return string
     */
    public function getWorkNotes(): string
    {
        return (string)$this["WORK_NOTES"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setWorkNotes(string $value)
    {
        $this["WORK_NOTES"] = $value;
    }


    /**
     * @return string
     */
    public function getAdminNotes(): string
    {
        return (string)$this["ADMIN_NOTES"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setAdminNotes(string $value)
    {
        $this["ADMIN_NOTES"] = $value;
    }


    /**
     * @return string
     */
    public function getStoredHash(): string
    {
        return (string)$this["STORED_HASH"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setStoredHash(string $value)
    {
        $this["STORED_HASH"] = $value;
    }


    /**
     * @return string
     */
    public function getXmlId(): string
    {
        return (string)$this["XML_ID"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setXmlId(string $value)
    {
        $this["XML_ID"] = $value;
    }


    /**
     * @return ?Date
     */
    public function getPersonalBirthday(): ?Date
    {
        return $this["PERSONAL_BIRTHDAY"] instanceof Date ? $this["PERSONAL_BIRTHDAY"] : null;
    }


    /**
     * @param Date $value
     * @return void
     */
    public function setPersonalBirthday(Date $value)
    {
        $this["PERSONAL_BIRTHDAY"] = $value;
    }


    /**
     * @return string
     */
    public function getExternalAuthId(): string
    {
        return (string)$this["EXTERNAL_AUTH_ID"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setExternalAuthId(string $value)
    {
        $this["EXTERNAL_AUTH_ID"] = $value;
    }


    /**
     * @return ?DateTime
     */
    public function getCheckwordTime(): ?DateTime
    {
        return $this["CHECKWORD_TIME"] instanceof DateTime ? $this["CHECKWORD_TIME"] : null;
    }


    /**
     * @param DateTime $value
     * @return void
     */
    public function setCheckwordTime(DateTime $value)
    {
        $this["CHECKWORD_TIME"] = $value;
    }


    /**
     * @return string
     */
    public function getConfirmCode(): string
    {
        return (string)$this["CONFIRM_CODE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setConfirmCode(string $value)
    {
        $this["CONFIRM_CODE"] = $value;
    }


    /**
     * @return int
     */
    public function getLoginAttempts(): int
    {
        return (int)$this["LOGIN_ATTEMPTS"];
    }


    /**
     * @param int $value
     * @return void
     */
    public function setLoginAttempts(int $value)
    {
        $this["LOGIN_ATTEMPTS"] = $value;
    }


    /**
     * @return ?DateTime
     */
    public function getLastActivityDate(): ?DateTime
    {
        return $this["LAST_ACTIVITY_DATE"] instanceof DateTime ? $this["LAST_ACTIVITY_DATE"] : null;
    }


    /**
     * @param DateTime $value
     * @return void
     */
    public function setLastActivityDate(DateTime $value)
    {
        $this["LAST_ACTIVITY_DATE"] = $value;
    }


    /**
     * @return string
     */
    public function getAutoTimeZone(): string
    {
        return (string)$this["AUTO_TIME_ZONE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setAutoTimeZone(string $value)
    {
        $this["AUTO_TIME_ZONE"] = $value;
    }


    /**
     * @return string
     */
    public function getTimeZone(): string
    {
        return (string)$this["TIME_ZONE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setTimeZone(string $value)
    {
        $this["TIME_ZONE"] = $value;
    }


    /**
     * @return int
     */
    public function getTimeZoneOffset(): int
    {
        return (int)$this["TIME_ZONE_OFFSET"];
    }


    /**
     * @param int $value
     * @return void
     */
    public function setTimeZoneOffset(int $value)
    {
        $this["TIME_ZONE_OFFSET"] = $value;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this["TITLE"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setTitle(string $value)
    {
        $this["TITLE"] = $value;
    }

    /**
     * @return string
     */
    public function getBxUserId(): string
    {
        return (string)$this["BX_USER_ID"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setBxUserId(string $value)
    {
        $this["BX_USER_ID"] = $value;
    }


    /**
     * @return string
     */
    public function getLanguageId(): string
    {
        return (string)$this["LANGUAGE_ID"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setLanguageId(string $value)
    {
        $this["LANGUAGE_ID"] = $value;
    }


    /**
     * @return string
     */
    public function getBlocked(): string
    {
        return (string)$this["BLOCKED"];
    }


    /**
     * @param string $value
     * @return void
     */
    public function setBlocked(string $value)
    {
        $this["BLOCKED"] = $value;
    }
}
