/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonIgnore;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "group_info")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "GroupInfo.findAll", query = "SELECT g FROM GroupInfo g"),
    @NamedQuery(name = "GroupInfo.findByGroupId", query = "SELECT g FROM GroupInfo g WHERE g.groupId = :groupId"),
    @NamedQuery(name = "GroupInfo.findByGroupName", query = "SELECT g FROM GroupInfo g WHERE g.groupName = :groupName")})
public class GroupInfo implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "group_id")
    private Long groupId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 45)
    @Column(name = "group_name")
    private String groupName;
    @Basic(optional = false)
    @NotNull
    @Lob
    @Size(min = 1, max = 65535)
    @Column(name = "group_url")
    private String groupUrl;
    @Basic(optional = false)
    @NotNull
    @Lob
    @Size(min = 1, max = 65535)
    @Column(name = "group_description")
    private String groupDescription;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "groupSrcId")
    private Collection<GroupInGroup> groupInGroupCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "groupTarId")
    private Collection<GroupInGroup> groupInGroupCollection1;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "groupTarId")
    private Collection<AnnoForGroup> annoForGroupCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "groupTarId")
    private Collection<UserBelongGroup> userBelongGroupCollection;

    public GroupInfo() {
    }

    public GroupInfo(Long groupId) {
        this.groupId = groupId;
    }

    public GroupInfo(Long groupId, String groupName, String groupUrl, String groupDescription) {
        this.groupId = groupId;
        this.groupName = groupName;
        this.groupUrl = groupUrl;
        this.groupDescription = groupDescription;
    }

    public Long getGroupId() {
        return groupId;
    }

    public void setGroupId(Long groupId) {
        this.groupId = groupId;
    }

    public String getGroupName() {
        return groupName;
    }

    public void setGroupName(String groupName) {
        this.groupName = groupName;
    }

    public String getGroupUrl() {
        return groupUrl;
    }

    public void setGroupUrl(String groupUrl) {
        this.groupUrl = groupUrl;
    }

    public String getGroupDescription() {
        return groupDescription;
    }

    public void setGroupDescription(String groupDescription) {
        this.groupDescription = groupDescription;
    }

    @XmlTransient     @JsonIgnore
    public Collection<GroupInGroup> getGroupInGroupCollection() {
        return groupInGroupCollection;
    }

    public void setGroupInGroupCollection(Collection<GroupInGroup> groupInGroupCollection) {
        this.groupInGroupCollection = groupInGroupCollection;
    }

    @XmlTransient     @JsonIgnore
    public Collection<GroupInGroup> getGroupInGroupCollection1() {
        return groupInGroupCollection1;
    }

    public void setGroupInGroupCollection1(Collection<GroupInGroup> groupInGroupCollection1) {
        this.groupInGroupCollection1 = groupInGroupCollection1;
    }

    @XmlTransient     @JsonIgnore
    public Collection<AnnoForGroup> getAnnoForGroupCollection() {
        return annoForGroupCollection;
    }

    public void setAnnoForGroupCollection(Collection<AnnoForGroup> annoForGroupCollection) {
        this.annoForGroupCollection = annoForGroupCollection;
    }

    @XmlTransient     @JsonIgnore
    public Collection<UserBelongGroup> getUserBelongGroupCollection() {
        return userBelongGroupCollection;
    }

    public void setUserBelongGroupCollection(Collection<UserBelongGroup> userBelongGroupCollection) {
        this.userBelongGroupCollection = userBelongGroupCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (groupId != null ? groupId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof GroupInfo)) {
            return false;
        }
        GroupInfo other = (GroupInfo) object;
        if ((this.groupId == null && other.groupId != null) || (this.groupId != null && !this.groupId.equals(other.groupId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.GroupInfo[ groupId=" + groupId + " ]";
    }
    
}
