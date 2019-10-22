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
     * 此事件在Worker进程/Task进程启动时发生。这里创建的对象可以在进程生命周期内使用
     *      onWorkerStart/onStart是并发执行的，没有先后顺序
     *      可以通过$server->taskworker属性来判断当前是Worker进程还是Task进程
     *      设置了worker_num和task_worker_num超过1时，每个进程都会触发一次onWorkerStart事件，可通过判断$worker_id区分不同的工作进程
     *      由 worker 进程向 task 进程发送任务，task 进程处理完全部任务之后通过onFinish回调函数通知 worker 进程。例如，我们在后台操作向十万个用户群发通知邮件，操作完成后操作的状态显示为发送中，这时我们可以继续其他操作。等邮件群发完毕后，操作的状态自动改为已发送。
     *
     * @param swoole_server $server
     * @param int           $worker_id
     */
    public function onWorkerStart(swoole_server $server, int $worker_id)
    {
        // Worker进程/Task进程启动 回调事件处理
        app()->getMainSwooleEvents()->doWorkerStart($server, $worker_id);
    }

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

        // 启动进程事件回调
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