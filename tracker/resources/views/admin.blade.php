@extends('layouts.app')

@section('content')
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5<br/> ADMIN</div>
                
                <?php if($data) {
                foreach ($data as $page => $modules) { ?>
                    <h3><?=$page;?></h3>
                    <table border="1" >
                        <thead>
                            <tr><th>block</th>
                            <th>hits</th>
                            <th>uniqIP</th>
                            <th>uniqCookie</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($modules as $type => $block) { ?>
                                <td colspan=4><?=$type;?></td>
                                    <?php foreach ($block as $nameBlock => $colums) { ?>
                                        <tr>
                                            <td><?=$nameBlock;?></td>
                                            <td><?=$colums['hits'];?></td>
                                            <td><?=$colums['ip'];?></td>
                                            <td><?=$colums['cookie'];?></td> 
                                        </tr>
                                    <?php } ?>
                                
                           
                         <?php } ?>
                         </tbody>
                     </table>
                <?php } } ?>
                </ul>
            </div>
        </div>
@endsection
