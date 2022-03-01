<?php
use App\system\Utils;
?>
<div class="page-section">
    <span class="title">Filtrar</span>
    <div class="content">
        <form method="get">
            <input type="hidden" name="filter" value="true">
            <lanel>período inicial</lanel>
            <div class="form-group">
                <input  class="sys-input" type="date" name="period_start" value="<?php $this->showOrNull('period_start')?>">
            </div>
            <lanel>período final</lanel>
            <div class="form-group">
                <input  class="sys-input" type="date" name="period_end" value="<?php $this->showOrNull('period_end')?>">
            </div>

            <lanel>Ordenação por nome</lanel>
            <div class="form-group">
                <select class="sys-input" name="order_name">
                    <option value="">Selecionar ordem</option>
                    <option <?php if($this->showOrNull('order_name',false) == 'ASC') {echo 'selected'; }?> value="ASC">Ordenar por nome ex: A-Z</option>
                    <option <?php if($this->showOrNull('order_name',false) == 'DESC') {echo 'selected'; }?>value="DESC">Ordenar por nome ex: Z-A</option>
                </select>
            </div>
            <lanel>Ordenação por data</lanel>
            <div class="form-group">
                <select class="sys-input" name="order_date">
                    <option value="">Selecionar ordem</option>
                    <option <?php if($this->showOrNull('order_date',false) == 'ASC') {echo 'selected'; }?> value="ASC">Ordenar por data ex: 1900-2000</option>
                    <option <?php if($this->showOrNull('order_date',false) == 'DESC') {echo 'selected'; }?> value="DESC">Ordenar por data ex: 2000-1900</option>
                </select>
            </div>
            <div class="action-container">
                <button type="submit" class="sys-btn">Aplicar filtros</button>
                <?php if(!empty($this->showOrNull('filter',false))):?>
                    <button id="clearFilters" type="submit" class="sys-btn">Limpar filtros</button>
                <?php endif;?>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <header class="w-30">
        <h3 class="title">Listagem de pontos de funcionarios</h3>
        <p class="description">Tela de listagem de pontos</p>
    </header>
    <main class="w-70">
        <table class="sys-table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Funcionário</th>
                <th>Hora inicial</th>
                <th>Hora final</th>
                <th>Data</th>
                <th>Data de registro</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($list)): ?>
                <?php foreach ($list as $entity): ?>
                    <tr>
                        <td>#<?php echo $entity->getId(); ?></td>
                        <td>
                            <?php
                                echo !empty($list_employees) && key_exists($entity->getEmployeeId(), $list_employees) ? $list_employees[$entity->getEmployeeId()]->getName() : "Funcionário não localizado";
                            ?>
                        </td>
                        <td><?php echo Utils\Security::securityString($entity->getStart()); ?></td>
                        <td><?php echo Utils\Security::securityString($entity->getEnd()); ?></td>
                        <td><?php echo Utils\Utils::formatDate(Utils\Security::securityString($entity->getDate())); ?></td>
                        <td><?php echo Utils\Utils::formatDate(Utils\Security::securityString($entity->getCreatedAt())); ?></td>
                        <td class="flex-td">
                            <a href="<?php $this->siteUrl("/ponto/atualizar/{$entity->getId()}") ?>"
                               class="sys-btn info w-auto"><i class="fas fa-edit"></i>Editar</a>
                            <button id="<?php echo $entity->getId() ?>" class="sys-btn danger w-auto _delete"><i class="fas fa-trash"></i>Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="action-container">
            <a href="<?php $this->siteUrl('/ponto/form') ?>" class="sys-btn success">
                <i class="fas fa-plus"></i>Adicionar novo</a>
        </div>
    </main>
</div>