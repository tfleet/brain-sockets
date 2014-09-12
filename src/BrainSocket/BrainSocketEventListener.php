<?php
	namespace BrainSocket;

	use Illuminate\Support\Facades\App;
	use Ratchet\MessageComponentInterface;
	use Ratchet\ConnectionInterface;

	class BrainSocketEventListener implements MessageComponentInterface {
		protected $clients;
		protected $response;

		public function __construct(BrainSocketResponseInterface $response)
		{
			$this->clients = new \SplObjectStorage;
			$this->response = $response;
		}

		public function onOpen(ConnectionInterface $conn)
		{
			echo "Connection Established! \n";
			$this->clients->attach($conn);
		}

		public function onMessage(ConnectionInterface $from, $msg)
		{

			$obj = json_decode($msg);
			$obj->client->data->message = 'badaboum boudoum boudoum';
			$obj = json_encode($obj);

			$this->testMethod($from,$obj);
		}

		public function testMethod(ConnectionInterface $from, $msg)
		{
			
			$from->send($this->response->make($msg));

			return 1;
		}

		public function onClose(ConnectionInterface $conn)
		{
			$this->clients->detach($conn);
			echo "Connection {$conn->resourceId} has disconnected\n";
		}

		public function onError(ConnectionInterface $conn, \Exception $e)
		{
			echo "An error has occurred: {$e->getMessage()}\n";
			$conn->close();
		}
	}