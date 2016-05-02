/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package admt.message;

import com.sun.grizzly.websockets.WebSocketClient;
import entity.Annotation;
import entity.Liveinterest;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.Socket;
import java.net.UnknownHostException;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.ejb.Stateless;
import org.codehaus.jackson.map.ObjectMapper;
import service.AnnotationFacadeREST;

/**
 *
 * @author panickos
 */
@Stateless
public class ConfluenceCommunications {
    
    public static final String addressAnnotations = "ws://localhost:21001";
    public static final String hostnameAnnotations = "localhost";
    public static final int portAnnotations = 21001;
    
    public static final String addressLiveinterests = "ws://localhost:21002";
    public static final String hostnameLiveinterests = "localhost";
    public static final int portLiveinterests = 21002;
    
    public void sendNewAnnotation(Annotation annotation) {
        DataMessage<entity.Annotation> message = new DataMessage<entity.Annotation>(DataMessage.MessageType.ANNOTATION_UPDATE, annotation, "annotations");
        //sendObject(message, addressAnnotations);
        sendObjectThroughSocket(message, hostnameAnnotations, portAnnotations);
    }
    
    public void sendNewLiveinterest(Liveinterest liveinterest) {
        DataMessage<entity.Liveinterest> message = new DataMessage<entity.Liveinterest>(DataMessage.MessageType.EXPRESSION_OF_INTEREST, liveinterest, "liveinterests");
        //sendObject(message, addressLiveinterests);
        sendObjectThroughSocket(message, hostnameLiveinterests, portLiveinterests);
    }
    
    public void sendRemoveLiveinterest(Liveinterest liveinterest) {
        DataMessage<entity.Liveinterest> message = new DataMessage<entity.Liveinterest>(DataMessage.MessageType.DELETE_INTEREST, liveinterest, "liveinterests");
        //sendObject(message, addressLiveinterests);
        sendObjectThroughSocket(message, hostnameLiveinterests, portLiveinterests);
    }
    
    
    void sendObject(DataMessage message, String address) {
        System.out.println("About to call Confluence");
        ObjectMapper mapper = new ObjectMapper();
        Logger.getLogger(ConfluenceCommunications.class.getName()).log(Level.INFO, "About to connect to Confluence");
        
        try {
            // This example will send a WS message to a server that echoes the message back
//            AsyncHttpClientConfig config = new AsyncHttpClientConfig.Builder().build();
//            AsyncHttpClient c = new AsyncHttpClient(new GrizzlyAsyncHttpProvider(config), config);
//            
//            WebSocketListener listener = new DefaultWebSocketListener() {
//                @Override
//                public void onMessage(String message) {
//                    System.out.println("Received message: " + message);
//                }
//            };
//            WebSocketUpgradeHandler handler = new WebSocketUpgradeHandler.Builder().addWebSocketListener(listener).build();
//            WebSocket socket = c.prepareGet(address).execute(handler).get();
//            
//            String msgStr = mapper.writeValueAsString(message);
//            socket.sendMessage(msgStr.getBytes("UTF-8"));
//            socket.close();
            
            
            WebSocketClient wsc = new WebSocketClient(address);
            
                wsc.connect();
                String msgStr = mapper.writeValueAsString(message);
                wsc.send(msgStr);
            
            
            wsc.send(msgStr);
            wsc.send(msgStr);
            wsc.close();
            //wsc.send("close");
            //wsc = null;
            
        } catch (Exception e) {
            System.out.println("The logger");
            Logger.getLogger(AnnotationFacadeREST.class.getName()).log(Level.SEVERE, null, e);
            
        }
    }
    
    void sendObjectThroughSocket(DataMessage message, String hostname, int port) {
        System.out.println("About to call Confluence (TCP socket)");
        ObjectMapper mapper = new ObjectMapper();
        Logger.getLogger(ConfluenceCommunications.class.getName()).log(Level.INFO, "About to connect to Confluence  (TCP socket)");
        try {
            Socket sock = new Socket(hostname, port);
            
            BufferedWriter out = new BufferedWriter(new OutputStreamWriter(sock.getOutputStream()));
            String msgStr = mapper.writeValueAsString(message);
            out.write(msgStr);
            out.newLine();
            out.flush();
            out.write(msgStr); 
            out.newLine();
            out.flush();
            out.close();
            sock.close();
            
        } catch (UnknownHostException ex) {
            Logger.getLogger(ConfluenceCommunications.class.getName()).log(Level.SEVERE, null, ex);
        } catch (IOException ex) {
            Logger.getLogger(ConfluenceCommunications.class.getName()).log(Level.SEVERE, null, ex);
        }
    }
}
