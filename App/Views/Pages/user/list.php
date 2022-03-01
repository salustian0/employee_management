<div class="box">
    <header class="w-30">
        <h3 class="title">Listagem de usuários</h3>
        <p class="description">Tela de listagem de usuários</p>
    </header>
    <main class="w-70">
        <table class="sys-table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Usuário</th>
                <th>Tipo de acesso</th>
                <th>Data de registro</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $entity): ?>
                    <tr>
                        <td>#<?php echo $entity->getId(); ?></td>
                        <td><?php echo \App\system\Utils\Security::securityString($entity->getUsername()); ?></td>
                        <td><?php echo \App\system\Utils\Security::securityString($entity->getAccess()); ?></td>
                        <td><?php echo \App\system\Utils\Security::securityString($entity->getCreatedAt()); ?></td>
                        <td class="flex-td">
                            <a href="<?php $this->siteUrl("/usuarios/atualizar/{$entity->getId()}") ?>"
                               class="sys-btn info w-auto"><i class="fas fa-edit"></i>Editar</a>
                            <button id="<?php echo $entity->getId() ?>" class="sys-btn danger w-auto _delete"><i class="fas fa-trash"></i>Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="action-container">
            <a href="<?php $this->siteUrl('/usuarios/form') ?>" class="sys-btn success">
                <i class="fas fa-plus"></i>Adicionar novo</a>
        </div>
    </main>
</div>