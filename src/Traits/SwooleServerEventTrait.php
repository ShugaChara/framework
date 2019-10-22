<?php
// +----------------------------------------------------------------------
// | Created by linshan. 版权所有 @
// +----------------------------------------------------------------------
// | Copyright (c) 2019 All rights reserved.
// +----------------------------------------------------------------------
// | Technology changes the world . Accumulation makes people grow .
// +----------------------------------------------------------------------
// | Author: kaka梦很美 <1099013371@qq.com>
// +----------------------------------------------------------------------

namespace ShugaChara\Framework\Traits;

use ShugaChara\Http\SwooleServerRequest;
use swoole_server;
use swoole_http_request;
use swoole_http_response;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait SwooleServerEventTrait
 * @package ShugaChara\Framework\Traits
 */
trait SwooleServerEventTrait
{
    /**
     * Start server process
     *
     * @param swoole_server $server
     * @return bool
     */
    public function onStart(swoole_server $server)
    {
        $swooleServer = swoole();
        if (version_compare(SWOOLE_VERSION, '1.9.5', '<')) {
            file_put_contents($swooleServer->getPidFile(), $server->master_pid);
        }

        process_rename($swooleServer->getServerName() . ' master');

        $swooleServer->consoleOutput->writeln(sprintf("当前 Swoole 服务 <info>%s</info> 已完成启动...", $swooleServer->getAppSwooleServerName()));
        $swooleServer->consoleOutput->writeln(sprintf("服务监听: <info>%s://%s:%s</info>", $swooleServer->getSwooleSocketTypeName(), $swooleServer->getServerHost(), $swooleServer->getServerPort()));
        $swooleServer->consoleOutput->writeln(sprintf('PID 进程文件: <info>%s</info>, PID: <info>%s</info>', $swooleServer->getPidFile(), $server->master_pid));
        $swooleServer->consoleOutput->writeln(sprintf('服务主进程 Master[<info>%s</info>] 已启动', $server->master_pid), OutputInterface::VERBOSITY_DEBUG);

        // 主进程事件回调
        app()->getMainSwooleEvents()->doStart($server);

        return true;
    }

    /**
     * Shutdown server process.
     *
     * @param swoole_server $server
     * @return void
     */
    public function onShutdown(swoole_server $server)
    {
        $swooleServer = swoole();
        if (file_exists($swooleServer->getPidFile())) {
            unlink($swooleServer->getPidFile());
        }

        $swooleServer->consoleOutput->writeln(sprintf('当前 Swoole 服务 <info>%s</info> 主进程[<info>%s</info>] 已停止运行 ', $swooleServer->getAppSwooleServerName(), $server->master_pid), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * @param swoole_http_request  $request
     * @param swoole_http_response $response
     */
    public function onRequest(swoole_http_request $swooleRequest, swoole_http_response $swooleResponse)
    {
        // 转接 Swoole request 对象
        $request = SwooleServerRequest::createServerRequestFromSwoole($swooleRequest);
        $response = app()->handleRequest($request);
        foreach ($response->getHeaders() as $key => $header) {
            $swooleResponse->header($key, $response->getHeaderLine($key));
        }
        foreach ($response->getCookieParams() as $key => $cookieParam) {
            $swooleResponse->cookie(
                $key,
                $cookieParam->getValue(),
                $cookieParam->getExpire(),
                $cookieParam->getPath(),
                $cookieParam->getDomain(),
                $cookieParam->isSecure(),
                $cookieParam->isHttpOnly()
            );
        }

        $swooleResponse->status($response->getStatusCode());
        $swooleResponse->end((string) $response->getBody());
        return;
    }
}