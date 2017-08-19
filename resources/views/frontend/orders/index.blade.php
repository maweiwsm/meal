@extends('layouts.frontend')

@section('content')
	<div class="container">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h1 class="panel-">饿单列表</h1>
			</div>
			@if (!empty($orders))
				<div class="table-responsive">
					<table class="table table-striped table-hover table-condensed">
						<thead>
							<tr class="h3">
								<th>单号</th>
								<th>标题</th>
								<th>金额</th>
								<th>状态</th>
								<th>发布人</th>
								<th>发布时间</th>
								<th>关闭时间</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
						@foreach ($orders as $order)
							<tr class="h4 bold {{ ($order->status == 1 ? 'success' : 'info') }}">
								<td class="table-text">{{ $order->id }}</td>
								<td class="table-text">{{ $order->title }}</td>
								<td class="table-text">100</td>
								<td class="table-text">{{ ($order->status == 1 ? '正在进行' : '已结束') }}</td>
								<td class="table-text">马玮</td>
								<td class="table-text">{{ $order->created_at }}</td>
								<td class="table-text">{{ $order->closed_at }}</td>
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
				<div class="panel-body">
					<span class="h3">还没有饿单哦~</span>
					<a href="{{ url('order.create') }}" class="btn btn-lg btn-success">发起点餐</a>
				</div>
			@endif
		</div>
	</div>
@endsection
