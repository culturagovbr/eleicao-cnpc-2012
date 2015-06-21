<?php if (!empty($this->usuario->id_usuario)) : ?>
<div id="menu_horizontal">
    <ul id="iconbar">
        <li>
            <a href="<?php echo $this->url(array('controller' => 'relatorios', 'action' => 'index'), '', true); ?>">
                <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-relatorios-mini.png" width="26">
                <span><br />Relatórios</span>
            </a>
        </li>
        <li style="height: 40px; padding: 0px;">
            <a href="<?php echo $this->url(array('controller' => 'forum', 'action' => 'index'), '', true); ?>">
                <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-forum-mini.png" width="30">
                <span><br />Fórum de debate</span>
            </a>
        </li>
        <?php if(date('YmdHis') >= '20121018000000' && date('YmdHis') < '20121023235959'): //real  ?>
        <?php //if(date('YmdHis') >= '20121017000000' && date('YmdHis') < '20121017163500'): //teste  ?>
        <li style="height: 40px; padding: 0px;">
            <a href="<?php echo $this->url(array('controller' => 'votacao', 'action' => 'index'), '', true); ?>">
                <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-votacao.png">
                <span style="line-height: 12px; padding-bottom: 5px;"><br />Vote aqui</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if($this->usuario->id_perfil > 1): ?>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'usuario', 'action' => 'index'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-cadastrar-usuario-mini.png" width="26">
                    <span><br />Manter usuário</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'recurso', 'action' => 'avaliacao-recurso-fase-um'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-recurso.png" width="26">
                    <span>Avaliar Recurso<br><font size="1px">(1&ordf; Instância)</font></span>
                </a>
            </li>
            <?php if($this->usuario->id_perfil == 4 || $this->usuario->id_perfil == 5):?>
                <li>
                    <a href="<?php echo $this->url(array('controller' => 'recurso', 'action' => 'avaliacao-recurso-fase-dois'), '', true); ?>">
                        <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-recurso-2.png" width="26">
                        <span>Avaliar Recurso<br><font size="1px">(2&ordf; Instância)</font></span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'avaliacao', 'action' => 'index'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-avaliar-cadastro.png" width="26">
                    <span><br />Avaliar Cadastro</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'habilitarsetorialuf', 'action' => 'habilitar-setorial-uf'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/ic-hablitar-setorial-uf.png" width="26">
                    <span>Habilitar Setorial/UF</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'recurso', 'action' => 'atualizar-informacoes-recurso'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-atualizar.png" width="26">
                    <span style="width: 400px;">Atualizar dados <br />do Recurso</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $this->url(array('controller' => 'avaliacao', 'action' => 'alterar-status-avaliacao-final'), '', true); ?>">
                    <img src="<?php echo $this->baseUrl(); ?>/public/img/icn-atualizar-status.png" width="26">
                    <span style="width: 400px;">Alterar status <br />final do Inscrito</span>
                </a>
            </li>
        <?php endif;?>
    </ul>
</div>
<?php endif;?>