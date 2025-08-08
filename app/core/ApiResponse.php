<?php 

namespace app\core;

class ApiResponse
{
    public static function error(int $code = 500, $data = []): void
    {
        switch ($code) {
            case 400:
                self::badRequest();
                break;
            case 401:
                self::unauthorized();
                break;
            case 403:
                self::forbidden();
                break;
            case 404:
                self::notFound();
                break;
            case 405:
                self::methodNotAllowed();
                break;
            case 422:
                self::validationError($data); 
                break;
            case 500:
            default:
                self::serverError();
                break;
        }
    }

    public static function ok(string $message = '요청이 성공적으로 처리되었습니다.', array $data = [])
    {
        JSON::make()
            ->success(true)
            ->status(200)
            ->message($message)
            ->data($data)
            ->response();
    }

    public static function created(string $message = '리소스가 성공적으로 생성되었습니다.', array $data = [])
    {
        JSON::make()
            ->success(true)
            ->status(201)
            ->message($message)
            ->data($data)
            ->response();
    }

    public static function methodNotAllowed(string $message = '허용되지 않은 요청 방식입니다.'): void
    {
        self::respond(405, $message);
    }
    
    public static function serverError(string $message = '서버 오류가 발생했습니다.'): void
    {
        self::respond(500, $message);
    }

    public static function notFound(string $message = '요청한 리소스를 찾을 수 없습니다.'): void
    {
        self::respond(404, $message);
    }

    public static function unauthorized(string $message = '인증이 필요합니다.'): void
    {
        self::respond(401, $message);
    }

    public static function forbidden(string $message = '이 요청은 허용되지 않습니다.'): void
    {
        self::respond(403, $message);
    }

    public static function badRequest(string $message = '잘못된 요청입니다.'): void
    {
        self::respond(400, $message);
    }


    public static function validationError(array $errors = []): void
    {
        JSON::make()
            ->success(false)
            ->status(422)
            ->message('입력값이 유효하지 않습니다.')
            ->data(['errors' => $errors])
            ->response();
    }

    public static function respond(int $status, string $message): void
    {
        JSON::make()
            ->success(false)
            ->status($status)
            ->message($message)
            ->response();
    }
}