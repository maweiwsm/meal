@extends('layouts.frontend')

@section('content')
	<div class="container">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h1 class="panel-">今日点餐</h1>
			</div>
			<div class="panel-body">
				@if (!empty($orders))
					<div class="table-responsive">
						<table class="table table-hover table-condensed">
							<thead>
							<th><h3>单号</h3></th>
							<th><h3>标题</h3></th>
							<th><h3>金额</h3></th>
							<th><h3>状态</h3></th>
							<th><h3>发布人</h3></th>
							<th><h3>发布时间</h3></th>
							<th><h3>关闭时间</h3></th>
							<th><h3>操作</h3></th>
							</thead>
							<tbody>
							@foreach ($orders as $order)
								<tr class="{{ ($order->status == 1 ? 'success' : 'danger') }}">
									<td class="table-text"><h4>{{ $order->id }}</h4></td>
									<td class="table-text"><h4>{{ $order->title }}</h4></td>
									<td class="table-text"><h4>100</h4></td>
									<td class="table-text"><h4>{{ ($order->status == 1 ? '正在进行' : '已结束') }}</h4></td>
									<td class="table-text"><h4>马玮</h4></td>
									<td class="table-text"><h4>{{ $order->created_at }}</h4></td>
									<td class="table-text"><h4>{{ $order->closed_at }}</h4></td>
									<td>
										<form action="/task/{{ $order->id }}" method="POST">
											{{ csrf_field() }}
											{{ method_field('DELETE') }}

											<a class="btn btn-success" href="{{ url('/order/'.$order->id) }}">加入</a>
											<a class="btn btn-success" href="{{ url('/order/'.$order->id.'/edit') }}">编辑</a>
											<button type="submit" id="delete-task-{{ $order->id }}" class="btn btn-danger">
												<i class="fa fa-btn fa-trash"></i>删除
											</button>
										</form>
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				@else
					<span class="h3">还没有人发起点餐哦~</span>
					<a href="{{ route('order.create') }}" class="btn btn-lg btn-success">发起点餐</a>
				@endif
			</div>
		</div>
	</div>
@endsection
