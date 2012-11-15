import std.stdio;
import std.socket;
import std.string;
import std.concurrency;
import core.thread;

void writeln2(A...)(A a)
if (is(typeof({writeln(a);}()))) {
	writeln(a);
	stdout.flush();
}

void main(){
    auto s = new Socket(AddressFamily.INET, SocketType.STREAM);
    auto addr = new InternetAddress("0.0.0.0", 9999);
    s.bind(addr);

    s.listen(1);
    
    do {
	    Socket conn = s.accept();
	    writefln("Connected by %s", conn.remoteAddress().toString());
	    
	    immutable socket_t handle = conn.handle();
	    auto tid = spawn(&handleClient,handle);
		
    } while(true);
    
}

void handleClient(immutable socket_t handle) {
	//s = receiveOnly!Socket();
	Socket s = new Socket(handle, AddressFamily.INET);
	
	char[1024] buf;
	
	while (true) {
		auto ret = s.receive(buf);
		if (ret == 0 || ret == Socket.ERROR) {
			if (ret == 0)
				writeln2("No data to receive");
			else
				writeln2("Socket error: ",s.getErrorText());
			break;
		}
		writeln2("Reveived from ",s.remoteAddress(),": ",buf[0..ret]);
		toUpperInPlace(buf[0..ret]);
		ret = s.send(buf[0..ret]);
		if (ret == Socket.ERROR) {
			writeln2("Socket send error: ",s.getErrorText());
			break;
		}
		writeln2("Sent ",ret," bytes: ",buf[0..ret]);
	}
	writeln2("Closing Connection from ",s.remoteAddress());
	s.close();
	
}
























/*
auto s = new Socket(AddressFamily.INET, SocketType.STREAM);
    auto addr = new InternetAddress("0.0.0.0", 9999);
    s.bind(addr);

    s.listen(1);
    
    do {
    auto conn = s.accept();

    writefln("Connected by %s", conn.remoteAddress().toString());
    
//		byte[1024] buf;
//		auto data = conn.receive(buf);
//		if (!data) break;
		conn.send("<h1>HELLO THERE THIS IS TEXT</h1>");
	conn.close();
	
    } while(true);
*/
