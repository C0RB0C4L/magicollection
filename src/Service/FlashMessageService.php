<?php

namespace App\Service;

final class FlashMessageService
{
    // used to help with the bootstrap color suffix
    public const TYPE_SUCCESS = "success";
    public const TYPE_WARNING = "warning";
    public const TYPE_ERROR = "danger";
    public const TYPE_INFO = "info";

    // used as a default message in the absence 
    public const MSG_SUCCESS = "flash.default.success";
    public const MSG_WARNING = "flash.default.warning";
    public const MSG_ERROR = "flash.default.error";
    public const MSG_INFO = "flash.default.info";
}
