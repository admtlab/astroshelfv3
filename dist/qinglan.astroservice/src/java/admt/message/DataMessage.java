/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package admt.message;

import java.io.Serializable;

/**
 *
 * @author panickos
 */
public class DataMessage<T> implements Serializable{

    public enum MessageType{
        EXPRESSION_OF_INTEREST,
        DELETE_INTEREST,
        ANNOTATION_UPDATE
    }
    
    private T data;
    
    private String channel;
    
    private MessageType messageType;
    
    public DataMessage(MessageType mt, T data, String channel) {
        this.data = data;
        this.messageType = mt;
        this.channel = channel;
    }

    public T getData() {
        return data;
    }

    public void setData(T data) {
        this.data = data;
    }

    public MessageType getMessageType() {
        return messageType;
    }

    public void setMessageType(MessageType messageType) {
        this.messageType = messageType;
    }
    
    public void setChannel(String channel) {
        this.channel = channel;
    }
    
    public String getChannel() {
        return channel;
    }
    
}
