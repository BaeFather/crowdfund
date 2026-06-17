import java.io.*;
import java.net.*;
import java.util.*;
import org.apache.log4j.*;		//로그처리용 패키


public class EnDeServer {
	private int port_va;			//서버 포트
	private int port_fm;
	private int port2;			//클라이언트 포트
	private String key;		//키
	private Logger logger;	//로그처리용
	private String host;
	private String corpId;
	private String enc;

	/**
	 * 생성자
	 */
	public EnDeServer(int port_va, int port_fm, String key, String host , int port2, String enc ) {
		try {
			logger = Logger.getLogger(EnDeServer.class);

			this.port_va = port_va;
			this.port_fm = port_fm;
			this.key = key;
			this.host = host;
			this.port2 = port2;
			this.enc = enc;
			
			ServerSocket serverSocket = new ServerSocket(port2);

			System.out.println(serverSocket.getInetAddress());
			System.out.println(serverSocket.getLocalPort());

			while (true) {
				Socket socket = serverSocket.accept();
				EnDeServerThread enDeServerThread = new EnDeServerThread(socket, key ,host,port_va, port_fm, port2, enc);
				enDeServerThread.start();
			}
		} catch (IOException ioe){ 
			logger.info(ioe);
		}
	}

	/**
	 * 실행
	 * @param args	커맨드라인 인자
	 */
	public static void main (String args[]) {

		try
		{
			
			
			//환경변수 로딩
			String configFile = System.getProperty("corpInfoFile");
			FileInputStream input = new FileInputStream(configFile); 
			Properties prop = new Properties();		
			prop.load(input);
			
			String enc		=	prop.getProperty("enc");			//1: 암호화, 0:비암호화
			String key			=	prop.getProperty("key");
			int port_va	=	Integer.parseInt(prop.getProperty("server_port_va"));
			int port_fm	=	Integer.parseInt(prop.getProperty("server_port_fm"));
			String host	=	prop.getProperty("server_ip");
			int port2	=	Integer.parseInt(prop.getProperty("client_port"));
			
			// log file
			PropertyConfigurator.configure(prop.getProperty("logInfoFile"));

			EnDeServer enDeServer = new EnDeServer(port_va, port_fm, key, host , port2, enc);

		} catch (IOException ioe){ 
			System.out.println(ioe);
		}
	}
}