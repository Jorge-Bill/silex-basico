<?php
use Symfony\Component\HttpFoundation\Request;

$site = $app['controllers_factory'];

$site->get('/home', function () use ($app) {
    return $app['view.renderer']->render('home');
});

$site->get('/', function () use ($app) {
    return $app['view.renderer']->render('default');
});

$site->get('/fale-conosco', function () use ($app) {
    return $app['view.renderer']->render('fale-conosco');
});

$site->post('/fale-conosco', function (Request $request) use ($app) {
    /** @var \Doctrine\DBAL\Connection $db */
    $db = $app['db'];
    $data = $request->request->all();
    if(!$data){
        $app->abort(404, "Post não encontrado!");
    }
    $db->insert('mensagens', [
        'nome' => $data['nome'],
        'email' => $data['email'],
        'mensagem' => $data['mensagem']
    ]);
    return $app->redirect('/listar-msg');
});

$site->get('/listar-msg', function () use ($app) {
    /** @var \Doctrine\DBAL\Connection $db */
    $db = $app['db'];
    $sql = "SELECT * FROM mensagens;";
    $msgs = $db->fetchAll($sql);
    return $app['view.renderer']->render('listar-msg', [
        'msgs' => $msgs
    ]);
});

$site->get('/delete/{id}', function ($id) use ($app) {
    /** @var \Doctrine\DBAL\Connection $db */
    $db = $app['db'];
    $sql = "SELECT * FROM mensagens WHERE id = ?;";
    $msg = $db->fetchAssoc($sql, [$id]);
    if(!$msg){
        $app->abort(404, "Mensagem não encontrada!");
    }
    $db->delete('mensagens', ['id' => $id]);
    return $app->redirect('/listar-msg');
});

$site->get('/limpar-msgs', function (Silex\Application $app) {
    $file = fopen(__DIR__ . '/../../data/truncate.sql', 'r');
    while ($line = fread($file, 4096)) {
        $app['db']->executeQuery($line);
    }
    fclose($file);
    return $app->redirect('/listar-msg');
});

return $site;
